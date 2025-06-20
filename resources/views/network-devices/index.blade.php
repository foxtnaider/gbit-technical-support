<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('OLT (Optical Line Terminal)') }}
            </h2>
            <a href="{{ route('network-devices.create') }}" class="inline-flex items-center px-4 py-2 bg-gbit-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gbit-orange-600 focus:bg-gbit-orange-600 active:bg-gbit-orange-700 focus:outline-none focus:ring-2 focus:ring-gbit-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Nuevo Dispositivo') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servidor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PON</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tecnología</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado de Registro</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($devices as $device)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="font-medium text-gray-900">{{ $device->olt_name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="font-medium text-gray-900 server-name" data-server-id="{{ $device->associated_server }}">Cargando...</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $device->pon_number ?? 'N/A' }}
                                            @if($device->max_onts_per_pon)
                                                <span class="text-xs text-gray-400 block">({{ $device->max_onts_per_pon }} ONTs)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $device->pon_types_supported ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $device->ip_address ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $device->status === 'active' ? 'bg-green-100 text-green-800' : ($device->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $device->status === 'active' ? 'Activo' : ($device->status === 'maintenance' ? 'Mantenimiento' : 'Inactivo') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $device->registration_status === 'registered' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $device->registration_status === 'registered' ? 'Registrado' : 'No Registrado' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('network-devices.show', $device) }}" class="text-blue-600 hover:text-blue-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                @if($device->registration_status !== 'registered')
                                                    <form action="{{ route('network-devices.register', $device) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Registrar OLT">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('network-devices.edit', $device) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('network-devices.destroy', $device) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que deseas eliminar este dispositivo?')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No hay dispositivos de red registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $devices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para cargar los datos de servidores
        function loadServerData() {
            // Obtener la URL base de la API desde la variable de entorno
            const apiUrl = '{{ env("API_GBIT_ADM") }}' + '/api/servers';
            const apiToken = '{{ env("X_API_TOKEN") }}';

            // Realizar la solicitud a la API
            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-TOKEN': apiToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const servers = data.data;
                    // Obtener todos los elementos con la clase server-name
                    const serverElements = document.querySelectorAll('.server-name');
                    
                    // Actualizar cada elemento con el nombre del servidor correspondiente
                    serverElements.forEach(element => {
                        const serverId = element.getAttribute('data-server-id');
                        if (serverId) {
                            // Buscar el servidor con el ID correspondiente
                            const server = servers.find(s => s.id == serverId);
                            if (server) {
                                element.textContent = server.nombre;
                            } else {
                                element.textContent = 'No encontrado';
                            }
                        } else {
                            element.textContent = 'N/A';
                        }
                    });
                } else {
                    console.error('Error al obtener la lista de servidores:', data.message);
                    const serverElements = document.querySelectorAll('.server-name');
                    serverElements.forEach(element => {
                        element.textContent = 'Error al cargar';
                    });
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                const serverElements = document.querySelectorAll('.server-name');
                serverElements.forEach(element => {
                    element.textContent = 'Error al cargar';
                });
            });
        }

        // Cargar datos cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', loadServerData);
        
        // Soporte para Turbolinks si está presente
        if (typeof Turbolinks !== 'undefined') {
            document.addEventListener('turbolinks:load', loadServerData);
        }
        
        // Soporte para Laravel Livewire si está presente
        if (typeof window.Livewire !== 'undefined') {
            document.addEventListener('livewire:load', loadServerData);
            document.addEventListener('livewire:update', loadServerData);
        }
        
        // Asegurarse de que la función se ejecute incluso si la página ya está cargada
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(loadServerData, 1);
        }
    </script>
</x-app-layout>
