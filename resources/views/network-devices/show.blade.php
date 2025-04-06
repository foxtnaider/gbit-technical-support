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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Información General</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Tipo</p>
                                    <p class="text-base">{{ strtoupper($networkDevice->type) }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Marca</p>
                                    <p class="text-base">{{ $networkDevice->brand }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Modelo</p>
                                    <p class="text-base">{{ $networkDevice->model }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Estado</p>
                                    <p class="text-base">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $networkDevice->status === 'active' ? 'bg-green-100 text-green-800' : ($networkDevice->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $networkDevice->status === 'active' ? 'Activo' : ($networkDevice->status === 'maintenance' ? 'Mantenimiento' : 'Inactivo') }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Descripción</p>
                                    <p class="text-base">{{ $networkDevice->description ?: 'No disponible' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Detalles PON</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Número de PON</p>
                                    <p class="text-base">{{ $networkDevice->pon_number ?: 'No disponible' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tipos de PON soportados</p>
                                    <p class="text-base">{{ $networkDevice->pon_types_supported ?: 'No disponible' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Capacidad Máxima de ONTs por PON</p>
                                    <p class="text-base">{{ $networkDevice->max_onts_per_pon ?: 'No disponible' }}</p>
                                </div>
                                
                            </div>
                            
                            <h3 class="text-lg font-semibold my-4">Conectividad</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Dirección IP</p>
                                    <p class="text-base">{{ $networkDevice->ip_address ?: 'No disponible' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-500">Puerto</p>
                                    <p class="text-base">{{ $networkDevice->port ?: 'No disponible' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Servidor Asociado</p>
                                    <p class="text-base">{{ $networkDevice->associated_server ?: 'No disponible' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Ubicación</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Latitud</p>
                                    <p class="text-base">{{ $networkDevice->latitude ?: 'No disponible' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Longitud</p>
                                    <p class="text-base">{{ $networkDevice->longitude ?: 'No disponible' }}</p>
                                </div>
                            </div>
                            
                            @if($networkDevice->latitude && $networkDevice->longitude)
                                <div class="w-full h-96 rounded-lg overflow-hidden shadow-lg">
                                    <div id="map" class="w-full h-full"></div>
                                </div>
                                
                                <script>
                                    function initMap() {
                                        const location = { lat: {{ $networkDevice->latitude }}, lng: {{ $networkDevice->longitude }} };
                                        const map = new google.maps.Map(document.getElementById("map"), {
                                            zoom: 15,
                                            center: location,
                                        });
                                        
                                        const marker = new google.maps.Marker({
                                            position: location,
                                            map: map,
                                            title: "{{ $networkDevice->brand }} {{ $networkDevice->model }}",
                                        });
                                    }
                                </script>
                                <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap"></script>
                            @else
                                <p class="text-gray-500 italic">No hay coordenadas disponibles para mostrar el mapa.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
