<?php

namespace App\Http\Controllers;

use App\Models\Nap;
use App\Models\NetworkDevice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
            'reference_power' => 'nullable|numeric',
            'fdt_distance' => 'nullable|numeric|min:0',
        ]);
        
        // Solo se valida que el número de PON no exceda la cantidad disponible en la OLT
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
            'reference_power' => 'nullable|numeric',
            'fdt_distance' => 'nullable|numeric|min:0',
        ]);
        
        // Solo se valida que el número de PON no exceda la cantidad disponible en la OLT
        $networkDevice = NetworkDevice::findOrFail($request->network_device_id);
        $ponNumber = (int) filter_var($request->pon_number, FILTER_SANITIZE_NUMBER_INT);
        
        if ($ponNumber > $networkDevice->pon_number) {
            throw ValidationException::withMessages([
                'pon_number' => 'El número de puerto PON seleccionado excede la cantidad de puertos disponibles en esta OLT.'
            ]);
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
        // Obtener la cantidad de NAPs asociadas a cada PON
        $napCounts = Nap::where('network_device_id', $networkDeviceId)
            ->select('pon_number', DB::raw('count(*) as total'))
            ->groupBy('pon_number')
            ->pluck('total', 'pon_number')
            ->toArray();

        for ($i = 1; $i <= $networkDevice->pon_number; $i++) {
            $ponName = "PON-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            $count = $napCounts[$ponName] ?? 0;
            $ponNumbers[] = [
                'name' => $count > 0 ? "$ponName (NAPs: $count)" : $ponName,
                'value' => $ponName,
                'disabled' => false // Nunca se deshabilita
            ];
        }
        return response()->json($ponNumbers);
    }
}
