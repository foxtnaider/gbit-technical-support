<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoreo de OLTs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Control de Monitoreo</h3>
                    
                    <div class="flex flex-wrap gap-4 mb-6">
                        <form action="{{ route('monitoring.run-check') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Verificar Todas las OLTs
                            </button>
                        </form>

                        <form action="{{ route('monitoring.clear-queue') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:border-yellow-800 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Limpiar Cola de Trabajos
                            </button>
                        </form>

                        <form action="{{ route('monitoring.restart-worker') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-800 focus:outline-none focus:border-purple-800 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Reiniciar Worker
                            </button>
                        </form>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Trabajos pendientes en cola: <span class="font-semibold">{{ $pendingJobs }}</span></p>
                    </div>

                    <div class="bg-gray-100 p-4 rounded-md">
                        <h4 class="font-medium text-gray-800 mb-2">Instrucciones:</h4>
                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                            <li><strong>Verificar Todas las OLTs:</strong> Inicia la verificación de todas las OLTs con dirección IP.</li>
                            <li><strong>Limpiar Cola de Trabajos:</strong> Elimina todos los trabajos pendientes en la cola.</li>
                            <li><strong>Reiniciar Worker:</strong> Reinicia el procesador de trabajos (útil si hay problemas).</li>
                            <li>Para verificar una OLT individual, usa el botón "Verificar" en la tabla de dispositivos.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dispositivos OLT</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca/Modelo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Ping</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Verificación</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($devices as $device)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $device->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $device->brand }}</div>
                                            <div class="text-sm text-gray-500">{{ $device->model }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $device->ip_address }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($device->status == 'active')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activo
                                                </span>
                                            @elseif ($device->status == 'inactive')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactivo
                                                </span>
                                            @elseif ($device->status == 'maintenance')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Mantenimiento
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $device->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($device->last_ping_status == 'accesible')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Accesible
                                                </span>
                                            @elseif ($device->last_ping_status == 'inaccesible')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inaccesible
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $device->last_ping_status ?? 'Desconocido' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $device->last_checked_at ? $device->last_checked_at->format('d/m/Y H:i:s') : 'Nunca' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('monitoring.check-device', $device->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900">Verificar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No hay dispositivos OLT registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Logs de Monitoreo</h3>
                    
                    <div class="text-gray-400 p-4 rounded-md overflow-x-auto">
                        <pre class="text-xs">@forelse ($logs as $log){{ $log }}@empty No hay logs de monitoreo disponibles.@endforelse</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
