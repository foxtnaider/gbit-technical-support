<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rendimiento OLT/ONU') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($error)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">¡Error de Conexión!</strong>
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            @endif

            @if(empty($olts) && !$error)
                <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <p class="text-gray-500 text-lg">No se encontraron OLTs o no hay datos de rendimiento disponibles.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($olts as $olt)
                        <div x-data="{ expanded: false }" class="bg-white rounded-lg shadow-sm overflow-hidden">
                            {{-- OLT Header --}}
                            <div @click="expanded = !expanded" class="p-4 md:p-6 cursor-pointer border-l-4 
                                    @if($olt['overallStatus'] === 'critical') border-red-500 
                                    @elseif($olt['overallStatus'] === 'warning') border-yellow-500 
                                    @elseif($olt['overallStatus'] === 'high_power_warning') border-orange-500
                                    @elseif($olt['overallStatus'] === 'offline') border-gray-400
                                    @else border-green-500 
                                    @endif">
                                <div class="flex justify-between items-center">
                                    <div class="flex-grow">
                                        <h3 class="text-lg font-bold text-gray-800">{{ $olt['name'] }}</h3>
                                        <p class="text-sm text-gray-500">{{ $olt['ip'] }}</p>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-center">
                                        <div>
                                            <p class="font-bold text-lg">{{ $olt['processedStats']['total'] }}</p>
                                            <p class="text-gray-500">ONUs</p>
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg text-red-500">{{ $olt['processedStats']['critical'] ?? 0 }}</p>
                                            <p class="text-gray-500">Críticas</p>
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg text-yellow-500">{{ $olt['processedStats']['warning'] ?? 0 }}</p>
                                            <p class="text-gray-500">Advertencias</p>
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg text-orange-500">{{ $olt['processedStats']['high_power_warning'] ?? 0 }}</p>
                                            <p class="text-gray-500">Alta Potencia</p>
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg text-gray-500">{{ $olt['processedStats']['offline'] ?? 0 }}</p>
                                            <p class="text-gray-500">Offline</p>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Collapsible PON Ports Section --}}
                            <div x-show="expanded" x-collapse class="bg-gray-50 p-4 md:p-6 border-t">
                                @if(empty($olt['ponPortsData']))
                                    <p class="text-sm text-gray-500">No hay datos de puertos PON para esta OLT.</p>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($olt['ponPortsData'] as $pon => $data)
                                            <div x-data="{ pon_expanded: false }" class="bg-white rounded-md border border-gray-200">
                                                {{-- PON Header --}}
                                                <div @click="pon_expanded = !pon_expanded" class="p-3 cursor-pointer">
                                                    <div class="flex justify-between items-center">
                                                        <h4 class="font-bold text-blue-700">PON {{ $pon }}</h4>
                                                        <div class="flex items-center space-x-2 text-xs">
                                                            <span title="Críticas" class="px-2 py-1 bg-red-100 text-red-700 rounded-full">{{ $data['stats']['critical'] ?? 0 }}</span>
                                                            <span title="Advertencias" class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full">{{ $data['stats']['warning'] ?? 0 }}</span>
                                                            <span title="Alta Potencia" class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full">{{ $data['stats']['high_power_warning'] ?? 0 }}</span>
                                                            <span title="Offline" class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full">{{ $data['stats']['offline'] ?? 0 }}</span>
                                                            <span title="Óptimas" class="px-2 py-1 bg-green-100 text-green-700 rounded-full">{{ $data['stats']['ok'] ?? 0 }}</span>
                                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': pon_expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Collapsible ONU List --}}
                                                <div x-show="pon_expanded" x-collapse class="border-t">
                                                    <table class="min-w-full text-sm">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-4 py-2 text-left font-medium text-gray-600">ONU Index</th>
                                                                <th class="px-4 py-2 text-right font-medium text-gray-600">RxPower</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            @foreach($data['onus'] as $onu)
                                                                @php
                                                                    $powerClass = 'text-gray-800';
                                                                    if ($onu['powerStatus'] === 'ok') $powerClass = 'text-green-600';
                                                                    if ($onu['powerStatus'] === 'warning') $powerClass = 'text-yellow-600';
                                                                    if ($onu['powerStatus'] === 'critical') $powerClass = 'text-red-600';
                                                                    if ($onu['powerStatus'] === 'high_power_warning') $powerClass = 'text-orange-600';
                                                                    if ($onu['powerStatus'] === 'offline') $powerClass = 'text-gray-500';
                                                                @endphp
                                                                <tr>
                                                                    <td class="px-4 py-2">ONU {{ $onu['onuIndex'] }}</td>
                                                                    <td class="px-4 py-2 text-right font-bold {{ $powerClass }}">
                                                                        {{ $onu['rxPower'] == 0 ? 'Offline' : $onu['rxPower'] . ' dBm' }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
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
</x-app-layout>
