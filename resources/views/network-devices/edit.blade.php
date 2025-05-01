<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Dispositivo de Red') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('network-devices.update', $networkDevice) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Sistema de pestañas -->
                        <div class="mb-6">
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-6">
                                    <button type="button" class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="info-general">
                                        Información General
                                    </button>
                                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="conexion">
                                        Conexión y Acceso
                                    </button>
                                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="detalles-pon">
                                        Detalles PON
                                    </button>
                                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="ubicacion">
                                        Ubicación
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <!-- Tipo de Dispositivo (oculto) -->
                        <input type="hidden" name="type" value="OLT">

                        <!-- Contenido de las pestañas -->
                        <div class="tab-content">
                            <!-- Pestaña: Información General -->
                            <div id="info-general-tab" class="tab-pane">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Estado -->
                                    <div>
                                        <x-input-label for="status" :value="__('Estado')" />
                                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="active" {{ $networkDevice->status == 'active' ? 'selected' : '' }}>Activo</option>
                                            <option value="inactive" {{ $networkDevice->status == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                            <option value="maintenance" {{ $networkDevice->status == 'maintenance' ? 'selected' : '' }}>En Mantenimiento</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </div>

                                    <!-- Nombre de la OLT -->
                                    <div>
                                        <x-input-label for="olt_name" :value="__('Nombre de la OLT')" />
                                        <x-text-input id="olt_name" class="block mt-1 w-full" type="text" name="olt_name" :value="old('olt_name', $networkDevice->olt_name)" required />
                                        <x-input-error :messages="$errors->get('olt_name')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Marca y Modelo -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="brand" :value="__('Marca')" />
                                        <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand', $networkDevice->brand)" required />
                                        <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="model" :value="__('Modelo')" />
                                        <x-text-input id="model" class="block mt-1 w-full" type="text" name="model" :value="old('model', $networkDevice->model)" required />
                                        <x-input-error :messages="$errors->get('model')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="mt-4">
                                    <x-input-label for="description" :value="__('Descripción')" />
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $networkDevice->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Pestaña: Conexión y Acceso -->
                            <div id="conexion-tab" class="tab-pane hidden">
                                <!-- Servidor Asociado -->
                                <div class="mb-4">
                                    <x-input-label for="associated_server" :value="__('Servidor Asociado')" />
                                    <div class="relative">
                                        <select id="associated_server" name="associated_server" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Seleccione un servidor</option>
                                        </select>
                                        <div id="server-loading" class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden">
                                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eyeIcon">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('associated_server')" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Servidor al que está asociado este dispositivo') }}</p>
                                </div>

                                <!-- Dirección IP -->
                                <div class="mb-4">
                                    <x-input-label for="ip_address" :value="__('Dirección IP')" />
                                    <x-text-input id="ip_address" class="block mt-1 w-full" type="text" name="ip_address" :value="old('ip_address', $networkDevice->ip_address)" placeholder="192.168.1.1" />
                                    <x-input-error :messages="$errors->get('ip_address')" class="mt-2" />
                                </div>

                                <!-- Puerto -->
                                <div class="mb-4">
                                    <x-input-label for="port" :value="__('Puerto')" />
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-1">
                                        <div>
                                            <x-text-input id="port" class="block w-full" type="text" name="port" :value="old('port', $networkDevice->port)" placeholder="22, 80, 443, etc." />
                                        </div>
                                        <div>
                                            <select id="port_type" name="port_type" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="">Seleccionar tipo...</option>
                                                <option value="ssh" {{ old('port_type', $networkDevice->port_type) == 'ssh' ? 'selected' : '' }}>SSH</option>
                                                <option value="web" {{ old('port_type', $networkDevice->port_type) == 'web' ? 'selected' : '' }}>Web</option>
                                                <option value="telnet" {{ old('port_type', $networkDevice->port_type) == 'telnet' ? 'selected' : '' }}>Telnet</option>
                                            </select>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('port')" class="mt-2" />
                                    <x-input-error :messages="$errors->get('port_type')" class="mt-2" />
                                </div>

                                <!-- Puerto Secundario -->
                                <div class="mb-4">
                                    <x-input-label for="secondary_port" :value="__('Puerto Secundario')" />
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-1">
                                        <div>
                                            <x-text-input id="secondary_port" class="block w-full" type="text" name="secondary_port" :value="old('secondary_port', $networkDevice->secondary_port)" placeholder="22, 80, 443, etc." />
                                        </div>
                                        <div>
                                            <select id="secondary_port_type" name="secondary_port_type" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="">Seleccionar tipo...</option>
                                                <option value="ssh" {{ old('secondary_port_type', $networkDevice->secondary_port_type) == 'ssh' ? 'selected' : '' }}>SSH</option>
                                                <option value="web" {{ old('secondary_port_type', $networkDevice->secondary_port_type) == 'web' ? 'selected' : '' }}>Web</option>
                                                <option value="telnet" {{ old('secondary_port_type', $networkDevice->secondary_port_type) == 'telnet' ? 'selected' : '' }}>Telnet</option>
                                            </select>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('secondary_port')" class="mt-2" />
                                    <x-input-error :messages="$errors->get('secondary_port_type')" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Puerto adicional para conectarse al dispositivo (opcional)') }}</p>
                                </div>

                                <!-- Credenciales de Acceso -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="username" :value="__('Usuario')" />
                                        <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $networkDevice->username)" />
                                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Usuario para acceder remotamente a la OLT') }}</p>
                                    </div>
                                    <div>
                                        <x-input-label for="password" :value="__('Contraseña')" />
                                        <div class="relative">
                                            <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" :value="old('password', $networkDevice->password)" />
                                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eyeIcon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eyeSlashIcon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Contraseña para acceder remotamente a la OLT') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña: Detalles PON -->
                            <div id="detalles-pon-tab" class="tab-pane hidden">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="pon_number" :value="__('Número de PON')" />
                                        <x-text-input id="pon_number" class="block mt-1 w-full" type="number" name="pon_number" :value="old('pon_number', $networkDevice->pon_number)" min="1" required />
                                        <x-input-error :messages="$errors->get('pon_number')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="pon_types_supported" :value="__('Tipos de PON soportados')" />
                                        <select id="pon_types_supported" name="pon_types_supported" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Seleccionar...</option>
                                            <option value="GPON" {{ old('pon_types_supported', $networkDevice->pon_types_supported) == 'GPON' ? 'selected' : '' }}>GPON</option>
                                            <option value="EPON" {{ old('pon_types_supported', $networkDevice->pon_types_supported) == 'EPON' ? 'selected' : '' }}>EPON</option>
                                            <option value="XGS-PON" {{ old('pon_types_supported', $networkDevice->pon_types_supported) == 'XGS-PON' ? 'selected' : '' }}>XGS-PON</option>
                                            <option value="10G-EPON" {{ old('pon_types_supported', $networkDevice->pon_types_supported) == '10G-EPON' ? 'selected' : '' }}>10G-EPON</option>
                                            <option value="Otro" {{ old('pon_types_supported', $networkDevice->pon_types_supported) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('pon_types_supported')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="max_onts_per_pon" :value="__('Capacidad Máxima de ONTs por PON')" />
                                        <x-text-input id="max_onts_per_pon" class="block mt-1 w-full" type="number" name="max_onts_per_pon" :value="old('max_onts_per_pon', $networkDevice->max_onts_per_pon)" min="1" />
                                        <x-input-error :messages="$errors->get('max_onts_per_pon')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Umbrales de Potencia -->
                                <div class="mt-6">
                                    <x-input-label :value="__('Umbrales de Potencia')" class="font-semibold text-lg" />
                                    <p class="text-sm text-gray-600 mb-2">{{ __('Valores críticos de referencia para monitoreo de potencia (en dBm).') }}</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                        <div>
                                            <x-input-label for="power_threshold_low" :value="__('Umbral Bajo')" />
                                            <x-text-input id="power_threshold_low" class="block mt-1 w-full" type="number" step="0.01" name="power_threshold_low" :value="old('power_threshold_low', $networkDevice->power_threshold_low)" />
                                            <x-input-error :messages="$errors->get('power_threshold_low')" class="mt-2" />
                                            <p class="text-xs text-gray-500 mt-1">{{ __('Valor por defecto: -8 dBm') }}</p>
                                        </div>
                                        <div>
                                            <x-input-label for="power_threshold_high" :value="__('Umbral Alto')" />
                                            <x-text-input id="power_threshold_high" class="block mt-1 w-full" type="number" step="0.01" name="power_threshold_high" :value="old('power_threshold_high', $networkDevice->power_threshold_high)" />
                                            <x-input-error :messages="$errors->get('power_threshold_high')" class="mt-2" />
                                            <p class="text-xs text-gray-500 mt-1">{{ __('Valor por defecto: -27 dBm') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña: Ubicación -->
                            <div id="ubicacion-tab" class="tab-pane hidden">
                                <!-- Dirección -->
                                <div class="mb-4">
                                    <x-input-label for="address" :value="__('Dirección')" />
                                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $networkDevice->address)" />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Ubicación física donde se encuentra instalada la OLT') }}</p>
                                </div>

                                <!-- Coordenadas -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="latitude" :value="__('Latitud')" />
                                        <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" :value="old('latitude', $networkDevice->latitude)" />
                                        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="longitude" :value="__('Longitud')" />
                                        <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" :value="old('longitude', $networkDevice->longitude)" />
                                        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">{{ __('Haz clic en el mapa para seleccionar una ubicación y obtener automáticamente las coordenadas.') }}</p>
                                </div>
                                
                                <!-- Mapa interactivo con Leaflet -->
                                <div id="map" class="w-full h-96 rounded-lg overflow-hidden shadow-lg"></div>
                                
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">{{ __('Nota: Puedes hacer clic en cualquier punto del mapa o arrastrar el marcador para obtener las coordenadas exactas.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('network-devices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Actualizar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Función para inicializar el mapa de Leaflet
    function initMap() {
        if (document.getElementById('map')) {
            // Añadir los estilos de Leaflet al head
            if (!document.getElementById('leaflet-css')) {
                const leafletCSS = document.createElement('link');
                leafletCSS.id = 'leaflet-css';
                leafletCSS.rel = 'stylesheet';
                leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                leafletCSS.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
                leafletCSS.crossOrigin = '';
                document.head.appendChild(leafletCSS);
            }

            // Cargar Leaflet JS si no está cargado
            if (typeof L === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
                script.crossOrigin = '';
                script.onload = function() {
                    initLeafletMap();
                };
                document.head.appendChild(script);
            } else {
                initLeafletMap();
            }
        }
    }

    function initLeafletMap() {
        // Obtener valores iniciales de latitud y longitud
        // Usar valores por defecto si están vacíos para evitar errores de sintaxis
        const lat = {{ !empty(old('latitude', $networkDevice->latitude)) ? old('latitude', $networkDevice->latitude) : 7.883303 }};
        const lng = {{ !empty(old('longitude', $networkDevice->longitude)) ? old('longitude', $networkDevice->longitude) : -67.474317 }};
        const location = [lat, lng];
        
        // Inicializar el mapa
        const map = L.map('map').setView(location, 15);
        
        // Añadir capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Añadir marcador arrastrable
        const marker = L.marker(location, {
            draggable: true,
            title: "{{ $networkDevice->olt_name ?: ($networkDevice->brand . ' ' . $networkDevice->model) }}"
        }).addTo(map);
        
        // Actualizar campos cuando se mueve el marcador
        marker.on('dragend', function() {
            const position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(6);
            document.getElementById('longitude').value = position.lng.toFixed(6);
        });
        
        // Actualizar marcador cuando se hace clic en el mapa
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
        });
        
        // Actualizar el marcador cuando se cambian los campos de latitud y longitud
        document.getElementById('latitude').addEventListener('change', updateMarkerPosition);
        document.getElementById('longitude').addEventListener('change', updateMarkerPosition);
        
        function updateMarkerPosition() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng]);
            }
        }
    }
    
    // Cargar Leaflet cuando se haga clic en la pestaña de ubicación
    const ubicacionTab = document.querySelector('[data-tab="ubicacion"]');
    if (ubicacionTab) {
        ubicacionTab.addEventListener('click', initMap);
    }
    
    // También cargar si estamos en la pestaña de ubicación inicialmente
    if (window.location.hash === '#ubicacion') {
        document.querySelector('[data-tab="ubicacion"]').click();
    } else {
        // Iniciar en la primera pestaña por defecto
        document.querySelector('[data-tab="info-general"]').click();
    }

    // Función para cargar los servidores desde la API
    function loadServers() {
        const serverSelect = document.getElementById('associated_server');
        const serverLoading = document.getElementById('server-loading');
        
        if (serverSelect) {
            serverLoading.classList.remove('hidden');
            
            fetch('{{ env('API_GBIT_ADM') }}/api/servers', {
                headers: {
                    'X-API-TOKEN': '{{ env('X_API_TOKEN') }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                serverLoading.classList.add('hidden');
                
                // Verificar si la respuesta contiene datos de servidores, independientemente del formato exacto
                if (data && (data.data || data.servers)) {
                    // Obtener la lista de servidores (adaptarse a diferentes formatos de respuesta)
                    const servers = data.data || data.servers || [];
                    
                    servers.forEach(server => {
                        const option = document.createElement('option');
                        option.value = server.id;
                        option.textContent = server.name || server.nombre;
                        
                        // Usar una variable JavaScript para la comparación en lugar de inserción directa de PHP
                        const currentServerId = '{{ $networkDevice->associated_server ?? "null" }}';
                        if (server.id.toString() === currentServerId) {
                            option.selected = true;
                        }
                        
                        serverSelect.appendChild(option);
                    });
                    
                    // Inicializar Select2 si está disponible
                    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn !== 'undefined' && typeof window.jQuery.fn.select2 !== 'undefined') {
                        window.jQuery(serverSelect).select2({
                            placeholder: 'Seleccione un servidor',
                            allowClear: true
                        });
                    }
                } else if (data.message) {
                    // Solo mostrar error si realmente hay un mensaje de error
                    console.error('Error al cargar servidores:', data.message);
                }
            })
            .catch(error => {
                serverLoading.classList.add('hidden');
                console.error('Error al cargar servidores:', error);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el botón para mostrar/ocultar contraseña
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');
        
        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Cambiar el icono
                eyeIcon.classList.toggle('hidden');
                eyeSlashIcon.classList.toggle('hidden');
            });
        }

        // Funcionamiento de las pestañas
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = button.getAttribute('data-tab');
                const tabPane = document.getElementById(`${tabId}-tab`);

                // Ocultar todas las pestañas
                tabPanes.forEach(pane => pane.classList.add('hidden'));

                // Mostrar la pestaña seleccionada
                tabPane.classList.remove('hidden');

                // Cambiar el estilo de los botones
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                button.classList.remove('border-transparent', 'text-gray-500');
                button.classList.add('border-blue-500', 'text-blue-600');
            });
        });

        // Cargar servidores desde la API
        loadServers();
    });
</script>
