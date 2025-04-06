<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <strong>¡Hay errores en el formulario!</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Información Personal -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="full_name" value="{{ __('Nombre Completo') }}" />
                                    <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name', $customer->full_name)" required autofocus />
                                    <x-input-error for="full_name" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="identity_document" value="{{ __('Documento de Identidad') }}" />
                                    <div class="flex">
                                        @php
                                            $docType = '';
                                            $docNumber = '';
                                            if ($customer->identity_document) {
                                                $docType = substr($customer->identity_document, 0, 1);
                                                $docNumber = substr($customer->identity_document, 1);
                                            }
                                        @endphp
                                        <select id="identity_document_type" name="identity_document_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-l-md shadow-sm w-20" required>
                                            <option value="V" {{ old('identity_document_type', $docType) == 'V' ? 'selected' : '' }}>V</option>
                                            <option value="J" {{ old('identity_document_type', $docType) == 'J' ? 'selected' : '' }}>J</option>
                                            <option value="E" {{ old('identity_document_type', $docType) == 'E' ? 'selected' : '' }}>E</option>
                                            <option value="P" {{ old('identity_document_type', $docType) == 'P' ? 'selected' : '' }}>P</option>
                                        </select>
                                        <x-text-input id="identity_document_number" class="block mt-0 w-full rounded-l-none" type="text" name="identity_document_number" :value="old('identity_document_number', $docNumber)" required placeholder="Número de documento" />
                                    </div>
                                    <x-input-error for="identity_document" class="mt-2" />
                                    <x-input-error for="identity_document_number" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="phone_number" value="{{ __('Número de Teléfono') }}" />
                                    <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number', $customer->phone_number)" required />
                                    <x-input-error for="phone_number" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="email" value="{{ __('Correo Electrónico') }}" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $customer->email)" />
                                    <x-input-error for="email" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ubicación</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="mt-6">
                                    <x-input-label for="address" value="{{ __('Dirección') }}" />
                                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $customer->address)" required />
                                    <x-input-error for="address" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <x-input-label for="latitude" value="{{ __('Latitud') }}" />
                                        <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" :value="old('latitude', $customer->latitude)" required />
                                        <x-input-error for="latitude" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="longitude" value="{{ __('Longitud') }}" />
                                        <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" :value="old('longitude', $customer->longitude)" required />
                                        <x-input-error for="longitude" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <div id="map" class="w-full h-96 rounded-lg shadow-md"></div>
                                    <p class="text-sm text-gray-500 mt-2">Haz clic en el mapa para actualizar la ubicación del cliente.</p>
                                </div>
                            </div>
                        </div>

                        @if($customer->connections->isNotEmpty())
                            @php $connection = $customer->connections->first(); @endphp
                            @php $ont = $connection->ont; @endphp

                            <!-- Información de Conexión -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Conexión</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="nap_id" value="{{ __('NAP') }}" />
                                        <select id="nap_id" name="nap_id" class="border-gray-300 focus:border-gbit-blue-500 focus:ring-gbit-blue-500 rounded-md shadow-sm block mt-1 w-full" required>
                                            <option value="">Seleccionar NAP</option>
                                            @foreach ($naps as $nap)
                                                <option value="{{ $nap->id }}" {{ old('nap_id', $connection->nap_id) == $nap->id ? 'selected' : '' }}>
                                                    {{ $nap->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="nap_id" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="nap_port" value="{{ __('Puerto de NAP') }}" />
                                        <select id="nap_port" name="nap_port" class="border-gray-300 focus:border-gbit-blue-500 focus:ring-gbit-blue-500 rounded-md shadow-sm block mt-1 w-full">
                                            <option value="">Seleccione un puerto</option>
                                            @if($customer->connections->isNotEmpty() && $customer->connections->first()->nap_port)
                                                <option value="{{ $customer->connections->first()->nap_port }}" selected>Puerto {{ $customer->connections->first()->nap_port }}</option>
                                            @endif
                                        </select>
                                        <x-input-error for="nap_port" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <x-input-label for="ont_serial_number" value="{{ __('Número de Serie ONT') }}" />
                                        <x-text-input id="ont_serial_number" class="block mt-1 w-full" type="text" name="ont_serial_number" :value="old('ont_serial_number', $ont->serial_number ?? '')" required />
                                        <x-input-error for="ont_serial_number" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="ont_model" value="{{ __('Modelo ONT') }}" />
                                        <x-text-input id="ont_model" class="block mt-1 w-full" type="text" name="ont_model" :value="old('ont_model', $ont->model ?? '')" required />
                                        <x-input-error for="ont_model" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <x-input-label for="ont_brand" value="{{ __('Marca ONT') }}" />
                                        <x-text-input id="ont_brand" class="block mt-1 w-full" type="text" name="ont_brand" :value="old('ont_brand', $ont->brand ?? '')" required />
                                        <x-input-error for="ont_brand" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="service_plan" value="{{ __('Plan de Servicio') }}" />
                                        <x-text-input id="service_plan" class="block mt-1 w-full" type="text" name="service_plan" :value="old('service_plan', $connection->service_plan)" required />
                                        <x-input-error for="service_plan" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <x-input-label for="installation_date" value="{{ __('Fecha de Instalación') }}" />
                                        <x-text-input id="installation_date" class="block mt-1 w-full" type="date" name="installation_date" :value="old('installation_date', $connection->installation_date->format('Y-m-d'))" required />
                                        <x-input-error for="installation_date" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="status" value="{{ __('Estado') }}" />
                                        <select id="status" name="status" class="border-gray-300 focus:border-gbit-blue-500 focus:ring-gbit-blue-500 rounded-md shadow-sm block mt-1 w-full" required>
                                            <option value="active" {{ old('status', $connection->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                            <option value="inactive" {{ old('status', $connection->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                            <option value="pending" {{ old('status', $connection->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                        </select>
                                        <x-input-error for="status" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Información Adicional -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Información Adicional</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="customer_observations" value="{{ __('Observaciones del Cliente') }}" />
                                        <textarea id="customer_observations" name="customer_observations" class="border-gray-300 focus:border-gbit-blue-500 focus:ring-gbit-blue-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('customer_observations', $customer->observations) }}</textarea>
                                        <x-input-error for="customer_observations" class="mt-2" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label for="connection_observations" value="{{ __('Observaciones de la Conexión') }}" />
                                        <textarea id="connection_observations" name="connection_observations" class="border-gray-300 focus:border-gbit-blue-500 focus:ring-gbit-blue-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('connection_observations', $connection->observations) }}</textarea>
                                        <x-input-error for="connection_observations" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Cliente') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
    <script>
        let map;
        let marker;

        function initMap() {
            // Coordenadas iniciales (las del cliente)
            const initialLat = {{ old('latitude', $customer->latitude) }};
            const initialLng = {{ old('longitude', $customer->longitude) }};
            
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: initialLat, lng: initialLng },
                zoom: 15,
            });

            // Crear marcador inicial
            marker = new google.maps.Marker({
                position: { lat: initialLat, lng: initialLng },
                map: map,
                draggable: true,
            });

            // Actualizar coordenadas al arrastrar el marcador
            google.maps.event.addListener(marker, 'dragend', function() {
                document.getElementById('latitude').value = marker.getPosition().lat();
                document.getElementById('longitude').value = marker.getPosition().lng();
            });

            // Permitir hacer clic en el mapa para mover el marcador
            google.maps.event.addListener(map, 'click', function(event) {
                marker.setPosition(event.latLng);
                document.getElementById('latitude').value = event.latLng.lat();
                document.getElementById('longitude').value = event.latLng.lng();
            });

            // Actualizar el mapa cuando se cambien manualmente las coordenadas
            document.getElementById('latitude').addEventListener('change', updateMapFromCoordinates);
            document.getElementById('longitude').addEventListener('change', updateMapFromCoordinates);
        }

        function updateMapFromCoordinates() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const newPosition = { lat: lat, lng: lng };
                marker.setPosition(newPosition);
                map.setCenter(newPosition);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const napSelect = document.getElementById('nap_id');
            const portSelect = document.getElementById('nap_port');
            const currentNapId = "{{ $customer->connections->isNotEmpty() ? $customer->connections->first()->nap_id : '' }}";
            const currentPort = "{{ $customer->connections->isNotEmpty() ? $customer->connections->first()->nap_port : '' }}";
            
            console.log('NAP actual:', currentNapId);
            console.log('Puerto actual:', currentPort);
            
            // Cargar puertos disponibles para la NAP seleccionada inicialmente
            if (napSelect.value && napSelect.value !== currentNapId) {
                loadAvailablePorts(napSelect.value);
            }
            
            napSelect.addEventListener('change', function() {
                const napId = this.value;
                console.log('NAP seleccionada:', napId);
                
                // Si seleccionamos la misma NAP, asegurarnos de que el puerto actual esté disponible
                if (napId === currentNapId) {
                    loadAvailablePorts(napId, currentPort);
                    return;
                }
                
                if (napId) {
                    loadAvailablePorts(napId);
                } else {
                    portSelect.innerHTML = '<option value="">Seleccione un puerto</option>';
                    portSelect.disabled = true;
                }
            });
            
            function loadAvailablePorts(napId, selectedPort = null) {
                portSelect.disabled = true;
                portSelect.innerHTML = '<option value="">Cargando puertos...</option>';
                
                // Usar la URL completa con el dominio actual
                const url = `${window.location.origin}/customers/get-available-ports/${napId}`;
                console.log('Solicitando puertos a:', url);
                
                fetch(url)
                    .then(response => {
                        console.log('Respuesta recibida:', response);
                        if (!response.ok) {
                            throw new Error(`Error HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Puertos disponibles:', data);
                        portSelect.innerHTML = '<option value="">Seleccione un puerto</option>';
                        
                        // Si estamos editando, incluir el puerto actual en las opciones
                        if (selectedPort) {
                            const option = document.createElement('option');
                            option.value = selectedPort;
                            option.textContent = `Puerto ${selectedPort}`;
                            option.selected = true;
                            portSelect.appendChild(option);
                        }
                        
                        if (data.length === 0 && !selectedPort) {
                            portSelect.innerHTML += '<option value="" disabled>No hay puertos disponibles</option>';
                        } else {
                            // Agregar los puertos disponibles, excluyendo el puerto actual si ya lo agregamos
                            data.forEach(port => {
                                if (port != selectedPort) {
                                    const option = document.createElement('option');
                                    option.value = port;
                                    option.textContent = `Puerto ${port}`;
                                    portSelect.appendChild(option);
                                }
                            });
                        }
                        
                        portSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error al cargar los puertos:', error);
                        portSelect.innerHTML = '<option value="">Error al cargar puertos</option>';
                        portSelect.disabled = true;
                    });
            }
        });
    </script>
    @endpush
</x-app-layout>
