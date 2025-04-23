<?php

namespace App\Http\Controllers;

use App\Models\Nap;
use App\Models\NetworkDevice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $networkDevices = NetworkDevice::where('type', 'olt')->where('status', 'active')->get();
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'total_ports' => 'required|integer|min:1',
            'available_ports' => 'required|integer|lte:total_ports',
            'connector_type' => 'required|in:UPC,APC,SC,LC,FC,ST,MPO,MTP,Other',
            'network_device_id' => 'required|exists:network_devices,id',
            'pon_number' => 'required|string',
        ]);

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
        $networkDevices = NetworkDevice::where('type', 'olt')->get();
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'total_ports' => 'required|integer|min:1',
            'available_ports' => 'required|integer|lte:total_ports',
            'connector_type' => 'required|in:UPC,APC,SC,LC,FC,ST,MPO,MTP,Other',
            'network_device_id' => 'required|exists:network_devices,id',
            'pon_number' => 'required|string',
        ]);

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
        
        // Generar números PON basados en la cantidad de PON del dispositivo
        for ($i = 1; $i <= $networkDevice->pon_number; $i++) {
            $ponNumbers[] = "PON-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        return response()->json($ponNumbers);
    }
}
