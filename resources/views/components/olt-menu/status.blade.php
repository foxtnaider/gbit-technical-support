<div>
    <h3 class="text-xl font-semibold mb-4">Estado de ONUs</h3>
    
    <div class="mb-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label for="olt-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por OLT</label>
                <select id="olt-filter" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">Todas las OLTs</option>
                    @foreach($olts as $olt)
                        <option value="{{ $olt->id }}" data-name="{{ $olt->olt_name ?: 'OLT-' . $olt->id }}">
                            {{ $olt->olt_name ?: 'OLT ' . $olt->id }} {{ $olt->ip_address ? '(' . $olt->ip_address . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-gray-500">Dispositivos encontrados: {{ count($olts) }}</p>
            </div>
            <div>
                <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select id="status-filter" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">Todos</option>
                    <option value="working">Online</option>
                    <option value="offline">Offline</option>
                    <option value="dyinggasp">Dying Gasp</option>
                </select>
            </div>
            <div class="self-end">
                <button id="update-status-btn" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                    Actualizar
                </button>
            </div>
        </div>
    </div>
    
    <div id="loading-indicator" class="hidden flex justify-center items-center my-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600">Cargando datos...</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Índice</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OLT</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Admin</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado OMCC</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Fase</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número Serial</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody id="onus-table-body" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Seleccione una OLT y presione Actualizar para ver los datos</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        <h4 class="text-lg font-semibold mb-2">Estadísticas PON</h4>
        <div id="pon-stats" class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Las estadísticas PON se cargarán aquí dinámicamente -->
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const updateButton = document.getElementById('update-status-btn');
            const oltFilter = document.getElementById('olt-filter');
            const statusFilter = document.getElementById('status-filter');
            const loadingIndicator = document.getElementById('loading-indicator');
            const onusTableBody = document.getElementById('onus-table-body');
            const ponStatsContainer = document.getElementById('pon-stats');
            
            // Obtener la URL base de la API desde la variable de entorno
            const apiBaseUrl = '{{ env("VITE_API_TRUNK_OLT_EXTERNAL", "https://whoami-gbit.duckdns.org") }}';
            
            updateButton.addEventListener('click', function() {
                const selectedOltOption = oltFilter.options[oltFilter.selectedIndex];
                const selectedOltId = oltFilter.value;
                
                if (!selectedOltId) {
                    alert('Por favor, seleccione una OLT');
                    return;
                }
                
                const oltName = selectedOltOption.getAttribute('data-name');
                const statusFilter = document.getElementById('status-filter').value;
                
                // Mostrar indicador de carga
                loadingIndicator.classList.remove('hidden');
                
                // Realizar la petición HTTP
                fetch(`${apiBaseUrl}/api/query/results/olt/${oltName}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Actualizar la tabla con los datos recibidos
                            updateOnusTable(data, statusFilter);
                            // Actualizar estadísticas PON - Nota: la nueva API no proporciona estadísticas PON directamente
                            // Generamos las estadísticas PON a partir de los datos de ONUs
                            const ponStats = generatePonStatsFromOnus(data);
                            updatePonStats(ponStats);
                        } else {
                            throw new Error(data.message || 'Error desconocido');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        onusTableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-red-500">
                                    Error al cargar los datos: ${error.message}
                                </td>
                            </tr>
                        `;
                        ponStatsContainer.innerHTML = '';
                    })
                    .finally(() => {
                        // Ocultar indicador de carga
                        loadingIndicator.classList.add('hidden');
                    });
            });
            
            function updateOnusTable(data, statusFilter) {
                // Verificar si hay datos y si tienen la estructura esperada
                if (!data.data || data.data.length === 0 || !data.data[0].rawData || !data.data[0].rawData.onus) {
                    onusTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No se encontraron ONUs para esta OLT
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                const onus = data.data[0].rawData.onus;
                const oltName = data.message.split('OLT ')[1] || 'OLT';
                
                if (!onus || onus.length === 0) {
                    onusTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No se encontraron ONUs para esta OLT
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                // Filtrar ONUs según el estado seleccionado
                let filteredOnus = onus;
                if (statusFilter) {
                    filteredOnus = onus.filter(onu => onu.phaseState === statusFilter);
                }
                
                if (filteredOnus.length === 0) {
                    onusTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No se encontraron ONUs con el estado seleccionado
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                // Generar filas de la tabla
                const rows = filteredOnus.map(onu => {
                    const statusClass = getStatusClass(onu.phaseState);
                    
                    return `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${onu.index}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${oltName}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${onu.adminState}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${onu.omccState}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="${statusClass} px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    ${onu.phaseState}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${onu.serialNumber}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                <a href="#" class="text-blue-600 hover:text-blue-900">Reiniciar</a>
                            </td>
                        </tr>
                    `;
                }).join('');
                
                onusTableBody.innerHTML = rows;
            }
            
            // Función para generar estadísticas PON a partir de los datos de ONUs
            function generatePonStatsFromOnus(data) {
                if (!data.data || data.data.length === 0 || !data.data[0].rawData || !data.data[0].rawData.onus) {
                    return null;
                }
                
                const onus = data.data[0].rawData.onus;
                const ponStats = {};
                
                // Agrupar ONUs por PON
                onus.forEach(onu => {
                    // El formato del índice es típicamente '1/1/X:Y' donde X es el número PON
                    const ponMatch = onu.index.match(/\d+\/\d+\/(\d+):/); 
                    if (ponMatch) {
                        const ponNumber = ponMatch[1];
                        
                        if (!ponStats[ponNumber]) {
                            ponStats[ponNumber] = {
                                total: 0,
                                working: 0,
                                offline: 0,
                                dyinggasp: 0
                            };
                        }
                        
                        ponStats[ponNumber].total++;
                        
                        switch (onu.phaseState.toLowerCase()) {
                            case 'working':
                                ponStats[ponNumber].working++;
                                break;
                            case 'offline':
                                ponStats[ponNumber].offline++;
                                break;
                            case 'dyinggasp':
                                ponStats[ponNumber].dyinggasp++;
                                break;
                        }
                    }
                });
                
                return ponStats;
            }
            
            function updatePonStats(ponStats) {
                if (!ponStats || Object.keys(ponStats).length === 0) {
                    ponStatsContainer.innerHTML = '<p class="text-sm text-gray-500">No hay estadísticas PON disponibles</p>';
                    return;
                }
                
                const statsHtml = Object.entries(ponStats).map(([pon, stats]) => {
                    const workingPercentage = stats.total > 0 ? Math.round((stats.working / stats.total) * 100) : 0;
                    
                    return `
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h5 class="font-medium text-gray-700">PON ${pon}</h5>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm">
                                    <span>Total ONUs:</span>
                                    <span class="font-medium">${stats.total}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>ONUs Activas:</span>
                                    <span class="font-medium">${stats.working}</span>
                                </div>
                                <div class="mt-2 h-2 bg-gray-200 rounded-full">
                                    <div class="h-full bg-green-500 rounded-full" style="width: ${workingPercentage}%"></div>
                                </div>
                                <div class="text-xs text-right mt-1 text-gray-500">${workingPercentage}% activas</div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                ponStatsContainer.innerHTML = statsHtml;
            }
            
            function getStatusClass(status) {
                switch (status.toLowerCase()) {
                    case 'working':
                        return 'bg-green-100 text-green-800';
                    case 'offline':
                        return 'bg-red-100 text-red-800';
                    case 'dyinggasp':
                        return 'bg-purple-100 text-purple-800';
                    default:
                        return 'bg-yellow-100 text-yellow-800';
                }
            }
        });
    </script>
</div>
