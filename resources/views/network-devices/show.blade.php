<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Dispositivo de Red') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('network-devices.edit', $networkDevice) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('network-devices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Volver') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                    <!-- Estado y nombre principal -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-bold">{{ $networkDevice->olt_name ?: ($networkDevice->brand . ' ' . $networkDevice->model) }}</h1>
                            <p class="text-gray-500">{{ $networkDevice->ip_address }}</p>
                        </div>
                        <div class="mt-2 md:mt-0">
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $networkDevice->status === 'active' ? 'bg-green-100 text-green-800' : ($networkDevice->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $networkDevice->status === 'active' ? 'Activo' : ($networkDevice->status === 'maintenance' ? 'Mantenimiento' : 'Inactivo') }}
                            </span>
                        </div>
                    </div>

                    <!-- Pestañas de navegación -->
                    <div class="border-b border-gray-200 mb-4 overflow-x-auto pb-1">
                        <nav class="-mb-px flex space-x-8 min-w-max" aria-label="Tabs">
                            <button class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="general">
                                Información General
                            </button>
                            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="connectivity">
                                Conectividad
                            </button>
                            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="pon">
                                Detalles PON
                            </button>
                            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="location">
                                Ubicación
                            </button>
                        </nav>
                    </div>

                    <!-- Contenido de las pestañas -->
                    <div class="tab-content">
                        <!-- Pestaña: Información General -->
                        <div id="general-tab" class="tab-pane">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-medium text-gray-700 mb-3">Información Básica</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Tipo</p>
                                            <p class="text-base">{{ strtoupper($networkDevice->type) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Nombre OLT</p>
                                            <p class="text-base">{{ $networkDevice->olt_name ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Marca</p>
                                            <p class="text-base">{{ $networkDevice->brand }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Modelo</p>
                                            <p class="text-base">{{ $networkDevice->model }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Descripción</p>
                                            <p class="text-base">{{ $networkDevice->description ?: 'No disponible' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña: Conectividad -->
                        <div id="connectivity-tab" class="tab-pane hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-medium text-gray-700 mb-3">Acceso al Dispositivo</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Dirección IP</p>
                                            <p class="text-base">{{ $networkDevice->ip_address ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Puerto</p>
                                            <p class="text-base">{{ $networkDevice->port ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Tipo de Puerto</p>
                                            <p class="text-base">{{ $networkDevice->port_type ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Puerto Secundario</p>
                                            <p class="text-base">{{ $networkDevice->secondary_port ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Tipo de Puerto Secundario</p>
                                            <p class="text-base">{{ $networkDevice->secondary_port_type ?: 'No disponible' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-medium text-gray-700 mb-3">Credenciales</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Nombre de Usuario</p>
                                            <p class="text-base">{{ $networkDevice->username ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Contraseña</p>
                                            <div class="flex items-center">
                                                <p id="password-display" class="text-base">••••••••</p>
                                                <button type="button" id="toggle-password" class="ml-2 text-blue-600 hover:text-blue-800 focus:outline-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eye-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eye-off-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <h3 class="font-medium text-gray-700 my-3">Servidor Asociado</h3>
                                    <div id="server-details">
                                        <div class="flex items-center">
                                            <div id="server-loading" class="ml-2">
                                                <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div id="server-info" class="mt-2 hidden">
                                            <div class="bg-gray-100 p-3 rounded-md text-sm">
                                                <p><span class="font-medium">Nombre:</span> <span id="server-nombre"></span></p>
                                                <p><span class="font-medium">IP:</span> <span id="server-ip"></span></p>
                                                <p><span class="font-medium">Puerto:</span> <span id="server-puerto"></span></p>
                                                <p><span class="font-medium">Tipo:</span> <span id="server-tipo"></span></p>
                                                <p><span class="font-medium">Dirección:</span> <span id="server-direccion"></span></p>
                                            </div>
                                        </div>
                                        <div id="server-error" class="mt-2 hidden">
                                            <p class="text-sm text-red-500">No se pudo cargar la información del servidor.</p>
                                        </div>
                                        <div id="server-no-data" class="mt-2 hidden">
                                            <p class="text-sm text-gray-500">No hay servidor asociado.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña: Detalles PON -->
                        <div id="pon-tab" class="tab-pane hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-medium text-gray-700 mb-3">Configuración PON</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Número de PON</p>
                                            <p class="text-base">{{ $networkDevice->pon_number ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Tipos de PON soportados</p>
                                            <p class="text-base">{{ $networkDevice->pon_types_supported ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Capacidad Máxima de ONTs por PON</p>
                                            <p class="text-base">{{ $networkDevice->max_onts_per_pon ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Umbral de Potencia Bajo</p>
                                            <p class="text-base">{{ $networkDevice->power_threshold_low ?: 'No disponible' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Umbral de Potencia Alto</p>
                                            <p class="text-base">{{ $networkDevice->power_threshold_high ?: 'No disponible' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-medium text-gray-700 mb-3">Estado de Monitoreo</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Última verificación</p>
                                            <p class="text-base">{{ $networkDevice->last_checked_at ? $networkDevice->last_checked_at->format('d/m/Y H:i:s') : 'Nunca' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Estado del último ping</p>
                                            <p class="text-base">
                                                @if($networkDevice->last_ping_status === 'success')
                                                    <span class="text-green-600">Exitoso</span>
                                                @elseif($networkDevice->last_ping_status === 'failed')
                                                    <span class="text-red-600">Fallido</span>
                                                @else
                                                    <span class="text-gray-500">No disponible</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña: Ubicación -->
                        <div id="location-tab" class="tab-pane hidden">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-medium text-gray-700 mb-3">Coordenadas</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Dirección</p>
                                        <p class="text-base">{{ $networkDevice->address ?: 'No disponible' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Coordenadas</p>
                                        <p class="text-base">
                                            @if($networkDevice->latitude && $networkDevice->longitude)
                                                {{ $networkDevice->latitude }}, {{ $networkDevice->longitude }}
                                            @else
                                                No disponible
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Mapa interactivo con Leaflet -->
                                <div id="map" class="w-full h-96 rounded-lg overflow-hidden shadow-lg mb-4"></div>
                                
                                <p class="text-sm text-gray-600">El mapa muestra la ubicación exacta del dispositivo según las coordenadas registradas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sistema de pestañas
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Desactivar todas las pestañas
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                });
                
                // Activar la pestaña seleccionada
                button.classList.remove('border-transparent', 'text-gray-500');
                button.classList.add('border-blue-500', 'text-blue-600');
                
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-tab`).classList.remove('hidden');
            });
        });
        
        // Cargar detalles del servidor si hay un servidor asociado
        const serverId = '{{ $networkDevice->associated_server }}';
        if (serverId && serverId !== '') {
            loadServerDetails(serverId);
        } else {
            document.getElementById('server-loading').classList.add('hidden');
            document.getElementById('server-no-data').classList.remove('hidden');
        }
        
        // Configurar el botón para mostrar/ocultar contraseña
        const togglePasswordBtn = document.getElementById('toggle-password');
        const passwordDisplay = document.getElementById('password-display');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeOffIcon = document.getElementById('eye-off-icon');
        const password = '{{ $networkDevice->password }}';
        
        let passwordVisible = false;
        
        togglePasswordBtn.addEventListener('click', function() {
            passwordVisible = !passwordVisible;
            
            if (passwordVisible) {
                passwordDisplay.textContent = password || 'No disponible';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordDisplay.textContent = '••••••••';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        });
        
        // Inicializar el mapa con Leaflet
        @if($networkDevice->latitude && $networkDevice->longitude)
            const map = L.map('map').setView([{{ $networkDevice->latitude }}, {{ $networkDevice->longitude }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
                subdomains: ['a', 'b', 'c']
            }).addTo(map);
            
            L.marker([{{ $networkDevice->latitude }}, {{ $networkDevice->longitude }}]).addTo(map)
                .bindPopup('{{ $networkDevice->olt_name ?: ($networkDevice->brand . " " . $networkDevice->model) }}')
                .openPopup();
        @else
            // Si no hay coordenadas, mostrar un mensaje en el contenedor del mapa
            document.getElementById('map').innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 rounded-lg"><p class="text-gray-500 italic">No hay coordenadas disponibles para mostrar el mapa.</p></div>';
        @endif
    });

    // Función para cargar los detalles del servidor desde la API
    function loadServerDetails(serverId) {
        const serverLoading = document.getElementById('server-loading');
        const serverInfo = document.getElementById('server-info');
        const serverError = document.getElementById('server-error');
        
        // Mostrar indicador de carga (ya está visible por defecto)
        
        // Obtener la URL base de la API desde la variable de entorno
        const apiUrl = '{{ env("API_GBIT_ADM") }}' + '/api/servers/' + serverId;
        const apiToken = '{{ env("X_API_TOKEN") }}';
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-API-TOKEN': apiToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta de la API');
            }
            return response.json();
        })
        .then(data => {
            // Ocultar indicador de carga
            serverLoading.classList.add('hidden');
            
            if (data.success && data.data) {
                // Mostrar información del servidor
                const server = data.data;
                
                document.getElementById('server-nombre').textContent = server.nombre || 'No disponible';
                document.getElementById('server-ip').textContent = server.ip || 'No disponible';
                document.getElementById('server-puerto').textContent = server.puerto || 'No disponible';
                document.getElementById('server-tipo').textContent = server.tipo || 'No disponible';
                document.getElementById('server-direccion').textContent = server.direccion || 'No disponible';
                
                // Mostrar el contenedor de información
                serverInfo.classList.remove('hidden');
            } else {
                console.error('Formato de respuesta inválido:', data);
                serverError.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error al cargar detalles del servidor:', error);
            serverLoading.classList.add('hidden');
            serverError.classList.remove('hidden');
        });
    }
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>
