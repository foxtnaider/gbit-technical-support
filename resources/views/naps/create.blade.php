<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Nodo de Acceso al Proveedor (NAP)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('naps.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información General -->
                            <div>
                                <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200">Información General</h3>
                                
                                <!-- Número de NAP -->
                                <div class="mb-4">
                                    <x-input-label for="nap_number" :value="__('Número de NAP')" />
                                    <x-text-input id="nap_number" class="block mt-1 w-full" type="text" name="nap_number" :value="old('nap_number')" required autofocus />
                                    <x-input-error :messages="$errors->get('nap_number')" class="mt-2" />
                                </div>

                                <!-- Estado -->
                                <div class="mb-4">
                                    <x-input-label for="status" :value="__('Estado')" />
                                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>En Mantenimiento</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                <!-- Fecha de Instalación -->
                                <div class="mb-4">
                                    <x-input-label for="installation_date" :value="__('Fecha de Instalación')" />
                                    <x-text-input id="installation_date" class="block mt-1 w-full" type="date" name="installation_date" :value="old('installation_date')" />
                                    <x-input-error :messages="$errors->get('installation_date')" class="mt-2" />
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <x-input-label for="description" :value="__('Descripción')" />
                                    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Conectividad y Puertos -->
                            <div>
                                <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200">Conectividad y Puertos</h3>
                                
                                <!-- Cantidad de Puertos Totales -->
                                <div class="mb-4">
                                    <x-input-label for="total_ports" :value="__('Cantidad de Puertos Totales')" />
                                    <x-text-input id="total_ports" class="block mt-1 w-full" type="number" name="total_ports" :value="old('total_ports')" required min="1" />
                                    <x-input-error :messages="$errors->get('total_ports')" class="mt-2" />
                                </div>

                                <!-- Cantidad de Puertos Disponibles -->
                                <div class="mb-4">
                                    <x-input-label for="available_ports" :value="__('Cantidad de Puertos Disponibles')" />
                                    <x-text-input id="available_ports" class="block mt-1 w-full" type="number" name="available_ports" :value="old('available_ports')" required min="0" />
                                    <x-input-error :messages="$errors->get('available_ports')" class="mt-2" />
                                </div>

                                <!-- Tipo de Conector -->
                                <div class="mb-4">
                                    <x-input-label for="connector_type" :value="__('Tipo de Conector')" />
                                    <select id="connector_type" name="connector_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        @foreach($connectorTypes as $type)
                                            <option value="{{ $type }}" {{ old('connector_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('connector_type')" class="mt-2" />
                                </div>

                                <!-- Relación con OLT -->
                                <h3 class="text-lg font-medium mb-4 mt-6 pb-2 border-b border-gray-200">Relación con OLT</h3>
                                
                                <!-- OLT Asociada -->
                                <div class="mb-4">
                                    <x-input-label for="network_device_id" :value="__('OLT Asociada')" />
                                    <select id="network_device_id" name="network_device_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Seleccionar OLT...</option>
                                        @foreach($networkDevices as $device)
                                            <option value="{{ $device->id }}" {{ old('network_device_id') == $device->id ? 'selected' : '' }}>
                                                {{ $device->brand }} {{ $device->model }} ({{ $device->ip_address }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('network_device_id')" class="mt-2" />
                                </div>

                                <!-- PON Asociado -->
                                <div class="mb-4">
                                    <x-input-label for="pon_number" :value="__('PON Asociado')" />
                                    <select id="pon_number" name="pon_number" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Seleccione primero una OLT</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('pon_number')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200">Ubicación</h3>
                            
                            <!-- Dirección -->
                            <div class="mb-4">
                                <x-input-label for="address" :value="__('Dirección')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <!-- Coordenadas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="latitude" :value="__('Latitud')" />
                                    <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" :value="old('latitude')" required />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="longitude" :value="__('Longitud')" />
                                    <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" :value="old('longitude')" required />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-2">{{ __('Puedes buscar una ubicación directamente en el mapa o introducir las coordenadas manualmente.') }}</p>
                            </div>
                            
                            <div class="w-full h-96 rounded-lg overflow-hidden shadow-lg">
                                <div id="map" class="w-full h-full"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('naps.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Crear NAP') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar el mapa
            function initMap() {
                const defaultLocation = { lat: 7.8833, lng: -67.6648 }; // Coordenadas por defecto (ajustar según necesidad)
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 13,
                    center: defaultLocation,
                });
                
                let marker = new google.maps.Marker({
                    position: defaultLocation,
                    map: map,
                    draggable: true,
                });
                
                // Actualizar coordenadas al arrastrar el marcador
                google.maps.event.addListener(marker, 'dragend', function() {
                    document.getElementById('latitude').value = marker.getPosition().lat();
                    document.getElementById('longitude').value = marker.getPosition().lng();
                });
                
                // Permitir hacer clic en el mapa para colocar el marcador
                google.maps.event.addListener(map, 'click', function(event) {
                    marker.setPosition(event.latLng);
                    document.getElementById('latitude').value = event.latLng.lat();
                    document.getElementById('longitude').value = event.latLng.lng();
                });
                
                // Si ya hay coordenadas en los campos, centrar el mapa allí
                const latField = document.getElementById('latitude');
                const lngField = document.getElementById('longitude');
                
                if (latField.value && lngField.value) {
                    const position = {
                        lat: parseFloat(latField.value),
                        lng: parseFloat(lngField.value)
                    };
                    marker.setPosition(position);
                    map.setCenter(position);
                }
                
                // Actualizar el mapa cuando se cambien manualmente las coordenadas
                latField.addEventListener('change', updateMarkerFromFields);
                lngField.addEventListener('change', updateMarkerFromFields);
                
                function updateMarkerFromFields() {
                    if (latField.value && lngField.value) {
                        const position = {
                            lat: parseFloat(latField.value),
                            lng: parseFloat(lngField.value)
                        };
                        marker.setPosition(position);
                        map.setCenter(position);
                    }
                }
            }
            
            // Cargar el API de Google Maps
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                const script = document.createElement('script');
                script.src = "https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap";
                script.async = true;
                script.defer = true;
                window.initMap = initMap;
                document.head.appendChild(script);
            } else {
                initMap();
            }
            
            // Manejo de la relación entre OLT y PON
            const networkDeviceSelect = document.getElementById('network_device_id');
            const ponSelect = document.getElementById('pon_number');
            
            networkDeviceSelect.addEventListener('change', function() {
                const deviceId = this.value;
                
                if (!deviceId) {
                    ponSelect.innerHTML = '<option value="">Seleccione primero una OLT</option>';
                    return;
                }
                
                // Limpiar el select de PON
                ponSelect.innerHTML = '<option value="">Cargando...</option>';
                
                // Obtener los PONs disponibles para esta OLT
                fetch(`/naps/get-pon-numbers/${deviceId}`)
                    .then(response => response.json())
                    .then(data => {
                        ponSelect.innerHTML = '';
                        
                        if (data.length === 0) {
                            ponSelect.innerHTML = '<option value="">No hay PONs disponibles</option>';
                            return;
                        }
                        
                        // Añadir opción por defecto
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = 'Seleccionar PON...';
                        ponSelect.appendChild(defaultOption);
                        
                        // Añadir las opciones de PON
                        data.forEach(pon => {
                            const option = document.createElement('option');
                            option.value = pon;
                            option.textContent = pon;
                            ponSelect.appendChild(option);
                        });
                        
                        // Seleccionar el valor anterior si existe
                        const oldValue = "{{ old('pon_number') }}";
                        if (oldValue) {
                            ponSelect.value = oldValue;
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar los PONs:', error);
                        ponSelect.innerHTML = '<option value="">Error al cargar PONs</option>';
                    });
            });
            
            // Validación de puertos disponibles vs totales
            const totalPortsInput = document.getElementById('total_ports');
            const availablePortsInput = document.getElementById('available_ports');
            
            function validatePorts() {
                const total = parseInt(totalPortsInput.value) || 0;
                const available = parseInt(availablePortsInput.value) || 0;
                
                if (available > total) {
                    availablePortsInput.setCustomValidity('Los puertos disponibles no pueden ser mayores que los puertos totales');
                } else {
                    availablePortsInput.setCustomValidity('');
                }
            }
            
            totalPortsInput.addEventListener('input', validatePorts);
            availablePortsInput.addEventListener('input', validatePorts);
            
            // Disparar el evento change si ya hay un valor seleccionado (por ejemplo, en caso de error de validación)
            if (networkDeviceSelect.value) {
                const event = new Event('change');
                networkDeviceSelect.dispatchEvent(event);
            }
        });
    </script>
</x-app-layout>
