<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ont;
use App\Models\Connection;
use App\Models\Nap;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('connections.nap', 'connections.ont')->paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $naps = Nap::where('status', 'active')
                   ->with('networkDevice')
                   ->get();
        $servicePlans = [
            'basic' => 'Plan Básico (10 Mbps)',
            'standard' => 'Plan Estándar (30 Mbps)',
            'premium' => 'Plan Premium (50 Mbps)',
            'business' => 'Plan Empresarial (100 Mbps)',
        ];
        return view('customers.create', compact('naps', 'servicePlans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Combinar tipo y número de documento de identidad
            if ($request->has('identity_document_type') && $request->has('identity_document_number')) {
                $request->merge([
                    'identity_document' => $request->identity_document_type . $request->identity_document_number
                ]);
            }

            // Validar los datos del cliente
            $validatedCustomer = $request->validate([
                'full_name' => 'required|string|max:255',
                'identity_document' => 'required|string|max:20|unique:customers,identity_document',
                'phone_number' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'customer_observations' => 'nullable|string',
            ]);

            // Renombrar el campo de observaciones del cliente
            if (isset($validatedCustomer['customer_observations'])) {
                $validatedCustomer['observations'] = $validatedCustomer['customer_observations'];
                unset($validatedCustomer['customer_observations']);
            }

            // Validar los datos de la ONT
            $validatedOnt = $request->validate([
                'ont_serial_number' => 'required|string|max:255|unique:onts,serial_number',
                'ont_model' => 'required|string|max:255',
                'ont_brand' => 'nullable|string|max:255',
            ]);

            // Mapear los campos de la ONT a los nombres correctos
            $ontData = [
                'serial_number' => $validatedOnt['ont_serial_number'],
                'model' => $validatedOnt['ont_model'],
                'brand' => $validatedOnt['ont_brand'] ?? 'Genérico',
            ];

            // Validar los datos de la conexión
            $validatedConnection = $request->validate([
                'nap_id' => 'required|exists:naps,id',
                'nap_port' => [
                    'nullable',
                    'integer',
                    'min:1',
                    Rule::unique('connections')->where(function ($query) use ($request) {
                        return $query->where('nap_id', $request->nap_id);
                    }),
                ],
                'service_plan' => 'required|string|in:basic,standard,premium,business',
                'installation_date' => 'nullable|date',
                'connection_observations' => 'nullable|string',
            ]);

            // Renombrar el campo de observaciones de la conexión
            if (isset($validatedConnection['connection_observations'])) {
                $validatedConnection['observations'] = $validatedConnection['connection_observations'];
                unset($validatedConnection['connection_observations']);
            }

            // Verificar que el puerto seleccionado sea válido para la NAP
            $nap = Nap::findOrFail($request->nap_id);
            if ($request->nap_port && $request->nap_port > $nap->total_ports) {
                return back()->withErrors(['nap_port' => 'El puerto seleccionado excede la cantidad de puertos disponibles en esta NAP.'])->withInput();
            }

            // Iniciar transacción para asegurar que todos los registros se guarden o ninguno
            DB::beginTransaction();

            try {
                // Crear el cliente
                $customer = Customer::create($validatedCustomer);

                // Crear la ONT
                $ont = Ont::create($ontData);

                // Crear la conexión
                $validatedConnection['customer_id'] = $customer->id;
                $validatedConnection['ont_id'] = $ont->id;
                $validatedConnection['status'] = 'active';
                
                // Asegurarse de que nap_port tenga un valor si no se proporcionó
                if (!isset($validatedConnection['nap_port']) || $validatedConnection['nap_port'] === null) {
                    $validatedConnection['nap_port'] = 0; // Valor por defecto
                }
                
                Connection::create($validatedConnection);

                // Actualizar el número de puertos disponibles en la NAP solo si se seleccionó un puerto
                if ($request->nap_port) {
                    $nap->available_ports = $nap->available_ports - 1;
                    $nap->save();
                }

                // Confirmar la transacción si todo fue exitoso
                DB::commit();

                return redirect()->route('customers.index')
                    ->with('success', 'Cliente registrado exitosamente.');
            } catch (\Exception $e) {
                // Revertir la transacción si algo falla
                DB::rollBack();
                throw $e; // Re-lanzar la excepción para que sea capturada por el bloque try/catch exterior
            }
                
        } catch (\Exception $e) {
            // Registrar el error para depuración
            \Log::error('Error al crear cliente: ' . $e->getMessage());
            
            // Devolver al formulario con mensaje de error
            return back()->withErrors(['general' => 'Error al registrar el cliente: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load('connections.nap', 'connections.ont');
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $customer->load('connections.nap', 'connections.ont');
        $naps = Nap::where('status', 'active')
                   ->with('networkDevice')
                   ->get();
        $servicePlans = [
            'basic' => 'Plan Básico (10 Mbps)',
            'standard' => 'Plan Estándar (30 Mbps)',
            'premium' => 'Plan Premium (50 Mbps)',
            'business' => 'Plan Empresarial (100 Mbps)',
        ];
        
        return view('customers.edit', compact('customer', 'naps', 'servicePlans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            // Combinar tipo y número de documento de identidad
            if ($request->has('identity_document_type') && $request->has('identity_document_number')) {
                $request->merge([
                    'identity_document' => $request->identity_document_type . $request->identity_document_number
                ]);
            }

            // Validar los datos del cliente
            $validatedCustomer = $request->validate([
                'full_name' => 'required|string|max:255',
                'identity_document' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('customers')->ignore($customer->id),
                ],
                'phone_number' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'customer_observations' => 'nullable|string',
            ]);

            // Renombrar el campo de observaciones del cliente
            if (isset($validatedCustomer['customer_observations'])) {
                $validatedCustomer['observations'] = $validatedCustomer['customer_observations'];
                unset($validatedCustomer['customer_observations']);
            }

            // Validar los datos de la ONT
            $validatedOnt = $request->validate([
                'ont_serial_number' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('onts', 'serial_number')->ignore($customer->connections->first()->ont_id),
                ],
                'ont_model' => 'nullable|string|max:255',
                'ont_brand' => 'nullable|string|max:255',
            ]);

            // Validar los datos de la conexión
            $validatedConnection = $request->validate([
                'nap_id' => 'required|exists:naps,id',
                'nap_port' => [
                    'nullable',
                    'integer',
                    'min:1',
                    Rule::unique('connections')->where(function ($query) use ($request) {
                        return $query->where('nap_id', $request->nap_id);
                    })->ignore($customer->connections->first()->id),
                ],
                'service_plan' => 'required|string|in:basic,standard,premium,business',
                'installation_date' => 'nullable|date',
                'connection_observations' => 'nullable|string',
            ]);

            // Renombrar el campo de observaciones de la conexión
            if (isset($validatedConnection['connection_observations'])) {
                $validatedConnection['observations'] = $validatedConnection['connection_observations'];
                unset($validatedConnection['connection_observations']);
            }

            // Verificar que el puerto seleccionado sea válido para la NAP
            $nap = Nap::findOrFail($request->nap_id);
            if ($request->nap_port && $request->nap_port > $nap->total_ports) {
                return back()->withErrors(['nap_port' => 'El puerto seleccionado excede la cantidad de puertos disponibles en esta NAP.'])->withInput();
            }

            // Actualizar la conexión
            $connection = $customer->connections->first();
            $ont = $connection->ont;
            
            // Iniciar transacción para asegurar que todos los registros se actualicen o ninguno
            DB::beginTransaction();
            
            try {
                // Actualizar el cliente
                $customer->update($validatedCustomer);

                // Actualizar la ONT
                $ont->update([
                    'serial_number' => $validatedOnt['ont_serial_number'],
                    'model' => $validatedOnt['ont_model'],
                    'brand' => $validatedOnt['ont_brand'] ?? $ont->brand,
                ]);

                // Actualizar la conexión
                $connection->update([
                    'nap_id' => $validatedConnection['nap_id'],
                    'nap_port' => $validatedConnection['nap_port'] ?? 0,
                    'service_plan' => $validatedConnection['service_plan'],
                    'installation_date' => $validatedConnection['installation_date'],
                    'observations' => $validatedConnection['observations'] ?? $connection->observations,
                ]);

                // Si el NAP o el puerto cambiaron, actualizar el número de puertos disponibles
                $napChanged = $connection->nap_id != $request->nap_id;
                $portChanged = $connection->nap_port != $request->nap_port;
                
                if ($napChanged || $portChanged) {
                    // Liberar el puerto anterior
                    if ($connection->nap_id && $connection->nap_port) {
                        $oldNap = Nap::findOrFail($connection->nap_id);
                        $oldNap->available_ports = $oldNap->available_ports + 1;
                        $oldNap->save();
                    }
                    
                    // Ocupar el nuevo puerto solo si se seleccionó uno
                    if ($request->nap_port) {
                        $nap->available_ports = $nap->available_ports - 1;
                        $nap->save();
                    }
                }
                
                // Confirmar la transacción si todo fue exitoso
                DB::commit();
                
                return redirect()->route('customers.index')
                    ->with('success', 'Cliente actualizado exitosamente.');
            } catch (\Exception $e) {
                // Revertir la transacción si algo falla
                DB::rollBack();
                throw $e; // Re-lanzar la excepción para que sea capturada por el bloque try/catch exterior
            }
        } catch (\Exception $e) {
            // Registrar el error para depuración
            \Log::error('Error al actualizar cliente: ' . $e->getMessage());
            
            // Devolver al formulario con mensaje de error
            return back()->withErrors(['general' => 'Error al actualizar el cliente: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Liberar los puertos de NAP utilizados por las conexiones del cliente
        foreach ($customer->connections as $connection) {
            $nap = $connection->nap;
            $nap->available_ports = $nap->available_ports + 1;
            $nap->save();
        }

        // Eliminar el cliente (las conexiones y ONTs se eliminarán en cascada)
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
    
    /**
     * Get available ports for a specific NAP.
     */
    public function getAvailablePorts($napId)
    {
        $nap = Nap::findOrFail($napId);
        $usedPorts = Connection::where('nap_id', $napId)->pluck('nap_port')->toArray();
        
        $availablePorts = [];
        for ($i = 1; $i <= $nap->total_ports; $i++) {
            if (!in_array($i, $usedPorts)) {
                $availablePorts[] = $i;
            }
        }
        
        // Asegurarse de que la respuesta sea un array, incluso si está vacío
        return response()->json($availablePorts, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
