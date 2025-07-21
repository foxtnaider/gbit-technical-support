<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rendimiento OLT/ONU') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if($error)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">{{ $error }}</span>
                        </div>
                    @endif

                    @if(empty($olts) && !$error)
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No se encontraron OLTs para mostrar.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($olts as $olt)
                                <div x-data="{ expanded: false }" class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 flex flex-col">
                                    <div @click="expanded = !expanded" class="p-6 cursor-pointer flex-grow">
                                        <div class="flex justify-between items-start">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $olt['name'] }}</h3>
                                            @php
                                                $statusClasses = [
                                                    'ok' => 'bg-green-100 text-green-800',
                                                    'warning' => 'bg-yellow-100 text-yellow-800',
                                                    'critical' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusText = [
                                                    'ok' => 'Óptimo',
                                                    'warning' => 'Advertencia',
                                                    'critical' => 'Crítico',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$olt['overallStatus']] ?? '' }}">
                                                {{ $statusText[$olt['overallStatus']] ?? 'Desconocido' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">{{ $olt['ip'] }}</p>
                                        <p class="text-sm text-blue-600 mt-2 font-medium">{{ $olt['ponPorts'] }} Puertos PON</p>
                                        <div class="mt-4 text-xs text-gray-600">
                                            <p>Total ONUs: <span class="font-bold">{{ $olt['processedStats']['total'] }}</span></p>
                                        </div>
                                    </div>
                                    <div x-show="expanded" x-collapse class="bg-gray-50 p-4 border-t border-gray-200">
                                        @if(empty($olt['ponPortsData']))
                                            <p class="text-sm text-gray-500">No hay datos de ONUs para esta OLT.</p>
                                        @else
                                            <div class="space-y-4">
                                                @foreach($olt['ponPortsData'] as $pon => $data)
                                                    <div class="bg-white p-3 rounded-md shadow-sm">
                                                        <h4 class="font-semibold text-blue-800">PON {{ $pon }} <span class="text-sm font-normal text-gray-600">({{ $data['totalOnus'] }} ONUs)</span></h4>
                                                        <ul class="mt-2 space-y-1 text-xs">
                                                            @foreach($data['onus'] as $onu)
                                                                @php
                                                                    $powerClass = '';
                                                                    if ($onu['powerStatus'] === 'ok') $powerClass = 'text-green-600';
                                                                    if ($onu['powerStatus'] === 'warning') $powerClass = 'text-yellow-600';
                                                                    if ($onu['powerStatus'] === 'critical') $powerClass = 'text-red-600';
                                                                @endphp
                                                                <li class="flex justify-between items-center">
                                                                    <span>ONU {{ $onu['onuIndex'] }}:</span>
                                                                    <span class="font-bold {{ $powerClass }}">
                                                                        {{ $onu['rxPower'] == 0 ? 'Desconectada' : $onu['rxPower'] . ' dBm' }}
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
