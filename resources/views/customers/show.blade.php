<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Cliente') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-gbit-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gbit-orange-600 focus:bg-gbit-orange-600 active:bg-gbit-orange-700 focus:outline-none focus:ring-2 focus:ring-gbit-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Volver') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Personal -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Información Personal</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nombre Completo</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->full_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Documento de Identidad</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->identity_document }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Número de Teléfono</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->phone_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Correo Electrónico</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->email ?? 'No especificado' }}</p>
                                </div>
                                @if($customer->observations)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Observaciones</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->observations }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Información de Conexión -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Información de Conexión</h3>
                            @if($customer->connections->isNotEmpty())
                                @php $connection = $customer->connections->first(); @endphp
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Estado de la Conexión</p>
                                        <p class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($connection->status == 'active') 
                                                    bg-green-100 text-green-800
                                                @elseif($connection->status == 'inactive') 
                                                    bg-red-100 text-red-800
                                                @elseif($connection->status == 'suspended') 
                                                    bg-yellow-100 text-yellow-800
                                                @else 
                                                    bg-blue-100 text-blue-800
                                                @endif">
                                                {{ $connection->formatted_status }}
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">NAP Asociada</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $connection->nap->nap_number }} - {{ $connection->nap->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Puerto de la NAP</p>
                                        <p class="mt-1 text-sm text-gray-900">Puerto {{ $connection->nap_port }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Número de Serie de la ONT</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $connection->ont->serial_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Modelo de la ONT</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $connection->ont->brand }} {{ $connection->ont->model }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Plan de Servicio</p>
                                        <p class="mt-1 text-sm text-gray-900">
                                            @switch($connection->service_plan)
                                                @case('basic')
                                                    Plan Básico (10 Mbps)
                                                    @break
                                                @case('standard')
                                                    Plan Estándar (30 Mbps)
                                                    @break
                                                @case('premium')
                                                    Plan Premium (50 Mbps)
                                                    @break
                                                @case('business')
                                                    Plan Empresarial (100 Mbps)
                                                    @break
                                                @default
                                                    {{ $connection->service_plan }}
                                            @endswitch
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Fecha de Instalación</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $connection->installation_date ? $connection->installation_date->format('d/m/Y') : 'No especificada' }}</p>
                                    </div>
                                    @if($connection->observations)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Observaciones de la Conexión</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $connection->observations }}</p>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Este cliente no tiene conexiones registradas.</p>
                            @endif
                        </div>

                        <!-- Ubicación -->
                        <div class="bg-white p-6 rounded-lg shadow-md md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Ubicación</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Dirección</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $customer->address }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Coordenadas</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $customer->latitude }}, {{ $customer->longitude }}</p>
                                    </div>
                                </div>
                                <div>
                                    <div id="map" class="w-full h-64 rounded-lg"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
    <script>
        function initMap() {
            const customerLocation = { 
                lat: {{ $customer->latitude }}, 
                lng: {{ $customer->longitude }} 
            };
            
            const map = new google.maps.Map(document.getElementById("map"), {
                center: customerLocation,
                zoom: 15,
            });
            
            const marker = new google.maps.Marker({
                position: customerLocation,
                map: map,
                title: "{{ $customer->full_name }}"
            });
        }
    </script>
    @endpush
</x-app-layout>
