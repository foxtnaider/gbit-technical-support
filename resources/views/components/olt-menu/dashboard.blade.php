<div class="bg-white shadow-md rounded-lg p-6" x-data="onuDashboard()">
    <h3 class="text-xl font-semibold mb-4">Panel de Control</h3>
    
    <!-- Totales de ONUs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h4 class="font-medium text-blue-700">OLTs Activas</h4>
            <p class="text-2xl font-bold">{{ $oltsActivas }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <h4 class="font-medium text-green-700">ONUs Conectadas</h4>
            <p class="text-2xl font-bold" x-text="formatNumber(statistics.totals?.working || 0)">0</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <h4 class="font-medium text-red-700">ONUs Desconectadas</h4>
            <p class="text-2xl font-bold" x-text="formatNumber(statistics.totals?.offline || 0)">0</p>
        </div>
        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
            <h4 class="font-medium text-amber-700">ONUs Dying Gasp</h4>
            <p class="text-2xl font-bold" x-text="formatNumber(statistics.totals?.dyinggasp || 0)">0</p>
        </div>
    </div>
    
    <!-- Resumen Total -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
        <h4 class="font-medium text-gray-700 mb-2">Resumen Total de ONUs</h4>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="mr-6">
                    <span class="text-gray-600">Total ONUs:</span>
                    <span class="font-bold ml-2" x-text="formatNumber(statistics.totals?.total || 0)">0</span>
                </div>
                <div class="mr-6">
                    <span class="text-green-600">Conectadas:</span>
                    <span class="font-bold ml-2" x-text="formatNumber(statistics.totals?.working || 0)">0</span>
                </div>
                <div class="mr-6">
                    <span class="text-red-600">Desconectadas:</span>
                    <span class="font-bold ml-2" x-text="formatNumber(statistics.totals?.offline || 0)">0</span>
                </div>
                <div>
                    <span class="text-amber-600">Dying Gasp:</span>
                    <span class="font-bold ml-2" x-text="formatNumber(statistics.totals?.dyinggasp || 0)">0</span>
                </div>
            </div>
            <button 
                @click="fetchStatistics()" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm flex items-center"
                :disabled="loading"
            >
                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Actualizando...' : 'Actualizar'">Actualizar</span>
            </button>
        </div>
    </div>
    
    <!-- Tarjetas de OLTs -->
    <div class="mt-6">
        <h4 class="font-medium mb-4">Estado de OLTs</h4>
        <template x-if="loading && !statistics.olts?.length">
            <div class="flex justify-center items-center h-32">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>
        </template>
        <template x-if="error">
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-red-700">
                <p x-text="error">Error al cargar los datos</p>
            </div>
        </template>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="(olt, index) in statistics.olts" :key="index">
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <h5 class="font-semibold text-lg mb-2" x-text="olt.name">Nombre OLT</h5>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total ONUs:</span>
                            <span class="font-bold" x-text="formatNumber(olt.total)">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-600">Conectadas:</span>
                            <span class="font-bold" x-text="formatNumber(olt.working)">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-red-600">Desconectadas:</span>
                            <span class="font-bold" x-text="formatNumber(olt.offline)">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-amber-600">Dying Gasp:</span>
                            <span class="font-bold" x-text="formatNumber(olt.dyinggasp)">0</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="flex h-full">
                                <div 
                                    class="bg-green-500 h-full" 
                                    :style="`width: ${olt.total > 0 ? (olt.working / olt.total * 100) : 0}%`"
                                ></div>
                                <div 
                                    class="bg-red-500 h-full" 
                                    :style="`width: ${olt.total > 0 ? (olt.offline / olt.total * 100) : 0}%`"
                                ></div>
                                <div 
                                    class="bg-amber-500 h-full" 
                                    :style="`width: ${olt.total > 0 ? (olt.dyinggasp / olt.total * 100) : 0}%`"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    
    <script>
        function onuDashboard() {
            return {
                statistics: {
                    olts: [],
                    totals: {
                        working: 0,
                        offline: 0,
                        dyinggasp: 0,
                        total: 0
                    }
                },
                loading: true,
                error: null,
                
                init() {
                    this.fetchStatistics();
                },
                
                fetchStatistics() {
                    this.loading = true;
                    this.error = null;
                    
                    const apiUrl = '{{ route("olt-commands.onu-statistics") }}';
                    
                    fetch(apiUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error al obtener estadísticas');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.statistics) {
                                this.statistics = data.statistics;
                            } else {
                                throw new Error(data.message || 'Datos inválidos');
                            }
                        })
                        .catch(err => {
                            this.error = err.message;
                            console.error('Error:', err);
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                
                formatNumber(num) {
                    return num.toLocaleString('es-ES');
                }
            }
        }
    </script>
</div>
