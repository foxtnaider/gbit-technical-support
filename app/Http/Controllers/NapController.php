<?php

namespace App\Http\Controllers;

use App\Models\Nap;
use App\Models\NetworkDevice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $naps = Nap::with('networkDevice')->paginate(10);
        return view('naps.index', compact('naps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Consulta más flexible para obtener OLTs (insensible a mayúsculas/minúsculas)
        $networkDevices = NetworkDevice::where(function($query) {
            $query->where('type', 'olt')
                  ->orWhere('type', 'OLT');
        })->get();
        
        $connectorTypes = ['UPC', 'APC', 'SC', 'LC', 'FC', 'ST', 'MPO', 'MTP', 'Other'];
        return view('naps.create', compact('networkDevices', 'connectorTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nap_number' => 'required|string|unique:naps,nap_number',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'installation_date' => 'nullable|date',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'total_ports' => 'required|integer|min:1',
            'available_ports' => 'required|integer|lte:total_ports',
            'connector_type' => 'required|in:UPC,APC,SC,LC,FC,ST,MPO,MTP,Other',
            'network_device_id' => 'required|exists:network_devices,id',
            'pon_number' => 'required|string',
        ]);
        
        // Verificar si el PON ya está asignado a otra NAP para esta OLT
        if (Nap::isPonAssigned($request->network_device_id, $request->pon_number)) {
            throw ValidationException::withMessages([
                'pon_number' => 'Este puerto PON ya está asignado a otra NAP en esta OLT.'
            ]);
        }
        
        // Verificar si la OLT tiene suficientes puertos PON
        $networkDevice = NetworkDevice::findOrFail($request->network_device_id);
        $ponNumber = (int) filter_var($request->pon_number, FILTER_SANITIZE_NUMBER_INT);
        
        if ($ponNumber > $networkDevice->pon_number) {
            throw ValidationException::withMessages([
                'pon_number' => 'El número de puerto PON seleccionado excede la cantidad de puertos disponibles en esta OLT.'
            ]);
        }

        Nap::create($validated);

        return redirect()->route('naps.index')
            ->with('success', 'NAP creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Nap $nap)
    {
        return view('naps.show', compact('nap'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nap $nap)
    {
        // Consulta más flexible para obtener OLTs (insensible a mayúsculas/minúsculas)
        $networkDevices = NetworkDevice::where(function($query) {
            $query->where('type', 'olt')
                  ->orWhere('type', 'OLT');
        })->get();
        
        $connectorTypes = ['UPC', 'APC', 'SC', 'LC', 'FC', 'ST', 'MPO', 'MTP', 'Other'];
        return view('naps.edit', compact('nap', 'networkDevices', 'connectorTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nap $nap)
    {
        $validated = $request->validate([
            'nap_number' => ['required', 'string', Rule::unique('naps')->ignore($nap)],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'installation_date' => 'nullable|date',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'total_ports' => 'required|integer|min:1',
            'available_ports' => 'required|integer|lte:total_ports',
            'connector_type' => 'required|in:UPC,APC,SC,LC,FC,ST,MPO,MTP,Other',
            'network_device_id' => 'required|exists:network_devices,id',
            'pon_number' => 'required|string',
        ]);
        
        // Solo verificar si el PON ya está asignado cuando se cambia la OLT o el número de PON
        if ($request->network_device_id != $nap->network_device_id || $request->pon_number != $nap->pon_number) {
            // Verificar si el PON ya está asignado a otra NAP para esta OLT
            if (Nap::isPonAssigned($request->network_device_id, $request->pon_number, $nap->id)) {
                throw ValidationException::withMessages([
                    'pon_number' => 'Este puerto PON ya está asignado a otra NAP en esta OLT.'
                ]);
            }
            
            // Verificar si la OLT tiene suficientes puertos PON
            $networkDevice = NetworkDevice::findOrFail($request->network_device_id);
            $ponNumber = (int) filter_var($request->pon_number, FILTER_SANITIZE_NUMBER_INT);
            
            if ($ponNumber > $networkDevice->pon_number) {
                throw ValidationException::withMessages([
                    'pon_number' => 'El número de puerto PON seleccionado excede la cantidad de puertos disponibles en esta OLT.'
                ]);
            }
        }

        $nap->update($validated);

        return redirect()->route('naps.index')
            ->with('success', 'NAP actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nap $nap)
    {
        $nap->delete();

        return redirect()->route('naps.index')
            ->with('success', 'NAP eliminada exitosamente.');
    }
    
    /**
     * Get PON numbers for a specific OLT.
     */
    public function getPonNumbers($networkDeviceId)
    {
        $networkDevice = NetworkDevice::findOrFail($networkDeviceId);
        $ponNumbers = [];
        
        // Obtener los PONs que ya están asignados a NAPs
        $assignedPons = Nap::where('network_device_id', $networkDeviceId)
            ->pluck('pon_number')
            ->toArray();
        
        // Generar números PON basados en la cantidad de PON del dispositivo
        for ($i = 1; $i <= $networkDevice->pon_number; $i++) {
            $ponName = "PON-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            
            // Verificar si este PON ya está asignado a una NAP
            if (in_array($ponName, $assignedPons)) {
                $ponNumbers[] = [
                    'name' => $ponName . ' (Ocupado)',
                    'value' => $ponName,
                    'disabled' => true
                ];
            } else {
                $ponNumbers[] = [
                    'name' => $ponName,
                    'value' => $ponName,
                    'disabled' => false
                ];
            }
        }
        
        return response()->json($ponNumbers);
    }
}
