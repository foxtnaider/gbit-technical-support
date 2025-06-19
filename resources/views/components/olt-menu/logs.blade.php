<div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-xl font-semibold mb-4">Registros del Sistema</h3>
    
    <div class="mb-6 flex items-end space-x-4">
        <div class="flex-1">
            <label for="log-date" class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
            <input type="date" id="log-date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
        </div>
        <div class="flex-1">
            <label for="log-level" class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
            <select id="log-level" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="">Todos los niveles</option>
                <option value="info">Información</option>
                <option value="warning">Advertencia</option>
                <option value="error">Error</option>
                <option value="critical">Crítico</option>
            </select>
        </div>
        <div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                Filtrar
            </button>
        </div>
    </div>
    
    <div class="overflow-hidden border border-gray-200 rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OLT</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensaje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-06-18 19:45:23</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">OLT Principal</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Info</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">Sistema iniciado correctamente</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-06-18 19:30:15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">OLT Secundaria</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Advertencia</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">ONU-003 con señal débil</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-06-18 19:15:42</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">OLT Principal</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Error</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">ONU-002 desconectada</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Mostrando <span class="font-medium">1</span> a <span class="font-medium">3</span> de <span class="font-medium">120</span> resultados
                </div>
                <div class="flex space-x-2">
                    <button class="bg-white border border-gray-300 rounded-md py-1 px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Anterior
                    </button>
                    <button class="bg-white border border-gray-300 rounded-md py-1 px-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
