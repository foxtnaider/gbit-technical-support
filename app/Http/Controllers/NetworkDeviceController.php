<?php

namespace App\Http\Controllers;

use App\Models\NetworkDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NetworkDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = NetworkDevice::latest()->paginate(10);
        return view('network-devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('network-devices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['olt', 'OLT', 'nap'])],
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'pon_number' => 'nullable|integer|min:1',
            'pon_types_supported' => 'nullable|string|max:255',
            'max_onts_per_pon' => 'nullable|integer|min:1',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|string|max:255',
            'port_type' => ['nullable', 'string', Rule::in(['ssh', 'web', 'telnet', ''])],
            'secondary_port' => 'nullable|string|max:255',
            'secondary_port_type' => ['nullable', 'string', Rule::in(['ssh', 'web', 'telnet', ''])],
            'associated_server' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'power_threshold_low' => 'nullable|numeric|max:0',
            'power_threshold_high' => 'nullable|numeric|max:0',
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'maintenance'])],
            'olt_name' => 'required|string|max:255',
        ]);

        NetworkDevice::create([
            'olt_name' => $request->olt_name,
            'type' => $request->type,
            'brand' => $request->brand,
            'model' => $request->model,
            'address' => $request->address,
            'pon_number' => $request->pon_number,
            'pon_types_supported' => $request->pon_types_supported,
            'max_onts_per_pon' => $request->max_onts_per_pon,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'port_type' => $request->port_type,
            'secondary_port' => $request->secondary_port,
            'secondary_port_type' => $request->secondary_port_type,
            'associated_server' => $request->associated_server,
            'username' => $request->username,
            'password' => $request->password,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
            'power_threshold_low' => $request->power_threshold_low ?? -8,
            'power_threshold_high' => $request->power_threshold_high ?? -27,
            'status' => $request->status,
        ]);

        return redirect()->route('network-devices.index')
            ->with('success', 'Dispositivo de red creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NetworkDevice $networkDevice)
    {
        return view('network-devices.show', compact('networkDevice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NetworkDevice $networkDevice)
    {
        return view('network-devices.edit', compact('networkDevice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NetworkDevice $networkDevice)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['olt', 'OLT', 'nap'])],
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'pon_number' => 'nullable|integer|min:1',
            'pon_types_supported' => 'nullable|string|max:255',
            'max_onts_per_pon' => 'nullable|integer|min:1',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|string|max:255',
            'port_type' => ['nullable', 'string', Rule::in(['ssh', 'web', 'telnet', ''])],
            'secondary_port' => 'nullable|string|max:255',
            'secondary_port_type' => ['nullable', 'string', Rule::in(['ssh', 'web', 'telnet', ''])],
            'associated_server' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'power_threshold_low' => 'nullable|numeric|max:0',
            'power_threshold_high' => 'nullable|numeric|max:0',
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'maintenance'])],
            'olt_name' => 'required|string|max:255',
        ]);

        $networkDevice->update([
            'olt_name' => $request->olt_name,
            'type' => $request->type,
            'brand' => $request->brand,
            'model' => $request->model,
            'address' => $request->address,
            'pon_number' => $request->pon_number,
            'pon_types_supported' => $request->pon_types_supported,
            'max_onts_per_pon' => $request->max_onts_per_pon,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'port_type' => $request->port_type,
            'secondary_port' => $request->secondary_port,
            'secondary_port_type' => $request->secondary_port_type,
            'associated_server' => $request->associated_server,
            'username' => $request->username,
            'password' => $request->password,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
            'power_threshold_low' => $request->power_threshold_low ?? $networkDevice->power_threshold_low,
            'power_threshold_high' => $request->power_threshold_high ?? $networkDevice->power_threshold_high,
            'status' => $request->status,
        ]);

        return redirect()->route('network-devices.index')
            ->with('success', 'Dispositivo de red actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NetworkDevice $networkDevice)
    {
        $networkDevice->delete();

        return redirect()->route('network-devices.index')
            ->with('success', 'Dispositivo de red eliminado exitosamente.');
    }
}
