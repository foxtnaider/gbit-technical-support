<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de NAP') }}: {{ $nap->nap_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('naps.edit', $nap) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('naps.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
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
                        <!-- Información General -->
                        <div>
                            <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200">Información General</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Número de NAP</p>
                                    <p class="mt-1">{{ $nap->nap_number }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Estado</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $nap->status === 'active' ? 'bg-green-100 text-green-800' : ($nap->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $nap->getFormattedStatusAttribute() }}
                                        </span>
                                    </p>
                                </div>
                                
                                @if($nap->installation_date)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Fecha de Instalación</p>
                                    <p class="mt-1">{{ $nap->installation_date->format('d/m/Y') }}</p>
                                </div>
                                @endif
                                
                                @if($nap->description)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Descripción</p>
                                    <p class="mt-1">{{ $nap->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Conectividad y Puertos -->
                        <div>
                            <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200">Conectividad y Puertos</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Puertos Totales</p>
                                    <p class="mt-1">{{ $nap->total_ports }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Puertos Disponibles</p>
                                    <p class="mt-1">{{ $nap->available_ports }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tipo de Conector</p>
                                    <p class="mt-1">{{ $nap->connector_type }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">OLT Asociada</p>
                                    @if($nap->networkDevice)
                                        <p class="mt-1">{{ $nap->networkDevice->olt_name ?? $nap->networkDevice->ip_address }}</p>
                                    @else
                                        <p class="mt-1"><span class="text-gray-400">No asignado</span></p>
                                    @endif
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">PON Asociado</p>
                                    <p class="mt-1">{{ $nap->pon_number }}</p>
                                </div>
                            </div>
                            
                            <!-- Gráfico de Puertos -->
                            <div class="mt-6">
                                <h4 class="text-md font-medium mb-2">Uso de Puertos</h4>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-gbit-blue-600 h-4 rounded-full" style="width: {{ ($nap->total_ports - $nap->available_ports) / $nap->total_ports * 100 }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs mt-1">
                                    <span>{{ $nap->total_ports - $nap->available_ports }} en uso</span>
                                    <span>{{ $nap->available_ports }} disponibles</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ubicación -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200">Ubicación</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Dirección</p>
                                    <p class="mt-1">{{ $nap->address }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Coordenadas</p>
                                    <p class="mt-1">{{ $nap->latitude }}, {{ $nap->longitude }}</p>
                                </div>
                            </div>
                            
                            <div class="w-full h-64 rounded-lg overflow-hidden shadow-lg">
                                <div id="map" class="w-full h-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar el mapa
            function initMap() {
                const location = { 
                    lat: {{ $nap->latitude }}, 
                    lng: {{ $nap->longitude }}
                };
                
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 15,
                    center: location,
                });
                
                const marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: "{{ $nap->nap_number }} - {{ $nap->name ?: 'NAP' }}"
                });
                
                const infowindow = new google.maps.InfoWindow({
                    content: `<div><strong>NAP: {{ $nap->nap_number }}</strong><br>
                              ${location.lat.toFixed(6)}, ${location.lng.toFixed(6)}</div>`
                });
                
                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });
                
                // Abrir el infowindow por defecto
                infowindow.open(map, marker);
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
        });
    </script>
</x-app-layout>
