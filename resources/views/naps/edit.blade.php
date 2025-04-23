<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Nodo de Acceso al Proveedor (NAP)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('naps.update', $nap) }}" class="space-y-6">
                        {{ csrf_field() }}
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Número de NAP -->
                            <div>
                                <x-input-label for="nap_number" :value="__('Número de NAP')" />
                                <x-text-input id="nap_number" class="block mt-1 w-full" type="text" name="nap_number" :value="old('nap_number', $nap->nap_number)" required autofocus />
                                <x-input-error :messages="$errors->get('nap_number')" class="mt-2" />
                            </div>
                            
                            <!-- Estado -->
                            <div>
                                <x-input-label for="status" :value="__('Estado')" />
                                <select id="status" name="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="active" {{ old('status', $nap->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactive" {{ old('status', $nap->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="maintenance" {{ old('status', $nap->status) == 'maintenance' ? 'selected' : '' }}>En Mantenimiento</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        
                            <!-- Fecha de Instalación -->
                            <div>
                                <x-input-label for="installation_date" :value="__('Fecha de Instalación')" />
                                <x-text-input id="installation_date" class="block mt-1 w-full" type="date" name="installation_date" :value="old('installation_date', $nap->installation_date?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('installation_date')" class="mt-2" />
                            </div>
                            
                            <!-- Dirección -->
                            <div>
                                <x-input-label for="address" :value="__('Dirección')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $nap->address)" required />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            
                            <!-- Coordenadas -->
                            <div>
                                <x-input-label for="latitude" :value="__('Latitud')" />
                                <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" :value="old('latitude', $nap->latitude)" required placeholder="-90 a 90" />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="longitude" :value="__('Longitud')" />
                                <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" :value="old('longitude', $nap->longitude)" required placeholder="-180 a 180" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>
                            
                            <!-- Puertos -->
                            <div>
                                <x-input-label for="total_ports" :value="__('Puertos Totales')" />
                                <x-text-input id="total_ports" class="block mt-1 w-full" type="number" name="total_ports" :value="old('total_ports', $nap->total_ports)" required min="1" />
                                <x-input-error :messages="$errors->get('total_ports')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="available_ports" :value="__('Puertos Disponibles')" />
                                <x-text-input id="available_ports" class="block mt-1 w-full" type="number" name="available_ports" :value="old('available_ports', $nap->available_ports)" required min="0" />
                                <x-input-error :messages="$errors->get('available_ports')" class="mt-2" />
                            </div>
                            
                            <!-- Tipo de Conector -->
                            <div>
                                <x-input-label for="connector_type" :value="__('Tipo de Conector')" />
                                <select id="connector_type" name="connector_type" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                    @foreach($connectorTypes as $type)
                                        <option value="{{ $type }}" {{ old('connector_type', $nap->connector_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('connector_type')" class="mt-2" />
                            </div>
                            
                            <!-- OLT Asociada -->
                            <div>
                                <x-input-label for="network_device_id" :value="__('OLT Asociada')" />
                                <select id="network_device_id" name="network_device_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="">Seleccionar OLT...</option>
                                    @foreach($networkDevices as $device)
                                        <option value="{{ $device->id }}" {{ old('network_device_id', $nap->network_device_id) == $device->id ? 'selected' : '' }}>
                                            {{ $device->olt_name }} ({{ $device->pon_number }} PONs)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('network_device_id')" class="mt-2" />
                            </div>
                            
                            <!-- PON Asociado -->
                            <div>
                                <x-input-label for="pon_number" :value="__('PON Asociado')" />
                                <select id="pon_number" name="pon_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="">Seleccione primero una OLT</option>
                                </select>
                                <x-input-error :messages="$errors->get('pon_number')" class="mt-2" />
                                <p id="pon_help_text" class="text-sm text-gray-500 mt-1 hidden">Seleccione un puerto PON disponible de la OLT.</p>
                            </div>
                            
                            <!-- Descripción -->
                            <div class="col-span-1 md:col-span-2">
                                <x-input-label for="description" :value="__('Descripción')" />
                                <textarea id="description" name="description" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('description', $nap->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar NAP') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const networkDeviceSelect = document.getElementById('network_device_id');
            const ponSelect = document.getElementById('pon_number');
            const ponHelpText = document.getElementById('pon_help_text');
            const totalPortsInput = document.getElementById('total_ports');
            const availablePortsInput = document.getElementById('available_ports');
            
            // Variables para almacenar el PON actual de la NAP
            const currentPonNumber = "{{ $nap->pon_number }}";
            const currentNetworkDeviceId = "{{ $nap->network_device_id }}";
            
            // Validar que los puertos disponibles no excedan los totales
            totalPortsInput.addEventListener('change', function() {
                const totalPorts = parseInt(this.value);
                const availablePorts = parseInt(availablePortsInput.value);
                
                if (availablePorts > totalPorts) {
                    availablePortsInput.value = totalPorts;
                }
                
                availablePortsInput.setAttribute('max', totalPorts);
            });
            
            networkDeviceSelect.addEventListener('change', function() {
                const deviceId = this.value;
                
                if (!deviceId) {
                    ponSelect.innerHTML = '<option value="">Seleccione primero una OLT</option>';
                    ponHelpText.classList.add('hidden');
                    return;
                }
                
                // Limpiar el select de PON
                ponSelect.innerHTML = '<option value="">Cargando...</option>';
                ponHelpText.classList.add('hidden');
                
                // Obtener los PONs disponibles para esta OLT
                fetch(`/naps/get-pon-numbers/${deviceId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        ponSelect.innerHTML = '';
                        
                        if (data.length === 0) {
                            ponSelect.innerHTML = '<option value="">No hay PONs disponibles</option>';
                            ponHelpText.textContent = 'Esta OLT no tiene puertos PON configurados.';
                            ponHelpText.classList.remove('hidden');
                            return;
                        }
                        
                        // Contar cuántos PONs están disponibles (excluyendo el PON actual de esta NAP)
                        const availablePons = data.filter(pon => !pon.disabled || (deviceId == currentNetworkDeviceId && pon.value == currentPonNumber)).length;
                        
                        if (availablePons === 0) {
                            ponHelpText.textContent = 'Todos los puertos PON de esta OLT están ocupados.';
                            ponHelpText.classList.remove('hidden');
                        } else if (deviceId == currentNetworkDeviceId) {
                            ponHelpText.textContent = `${availablePons} puerto(s) PON disponible(s) de un total de ${data.length}, incluyendo el puerto actual.`;
                            ponHelpText.classList.remove('hidden');
                        } else {
                            ponHelpText.textContent = `${availablePons} puerto(s) PON disponible(s) de un total de ${data.length}.`;
                            ponHelpText.classList.remove('hidden');
                        }
                        
                        // Añadir opción por defecto
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = 'Seleccionar PON...';
                        ponSelect.appendChild(defaultOption);
                        
                        // Añadir las opciones de PON
                        data.forEach(pon => {
                            const option = document.createElement('option');
                            option.value = pon.value;
                            option.textContent = pon.name;
                            
                            // Si es el PON actual de esta NAP, permitir seleccionarlo aunque esté ocupado
                            if (pon.disabled && !(deviceId == currentNetworkDeviceId && pon.value == currentPonNumber)) {
                                option.disabled = true;
                            }
                            
                            ponSelect.appendChild(option);
                        });
                        
                        // Seleccionar el valor anterior si existe
                        const oldValue = "{{ old('pon_number', $nap->pon_number) }}";
                        if (oldValue) {
                            ponSelect.value = oldValue;
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar los PONs:', error);
                        ponSelect.innerHTML = '<option value="">Error al cargar PONs</option>';
                        ponHelpText.textContent = 'Ocurrió un error al cargar los puertos PON. Por favor, intente nuevamente.';
                        ponHelpText.classList.remove('hidden');
                    });
            });
            
            // Cargar PONs iniciales si hay una OLT seleccionada
            if (networkDeviceSelect.value) {
                networkDeviceSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>
