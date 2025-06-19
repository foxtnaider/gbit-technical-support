<div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-xl font-semibold mb-4">Configuración de OLT</h3>
    
    <div class="mb-6">
        <label for="config-olt-select" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar OLT</label>
        <select id="config-olt-select" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            <option value="">Seleccione una OLT</option>
            <option value="1">OLT Principal - 192.168.1.1</option>
            <option value="2">OLT Secundaria - 192.168.1.2</option>
            <option value="3">OLT Respaldo - 192.168.1.3</option>
        </select>
    </div>
    
    <div class="space-y-6">
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-medium">Configuración Básica</h4>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="olt-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de OLT</label>
                    <input type="text" id="olt-name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Nombre de OLT">
                </div>
                <div>
                    <label for="olt-ip" class="block text-sm font-medium text-gray-700 mb-1">Dirección IP</label>
                    <input type="text" id="olt-ip" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="192.168.1.1">
                </div>
                <div>
                    <label for="olt-location" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                    <input type="text" id="olt-location" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Ubicación de OLT">
                </div>
            </div>
        </div>
        
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-medium">Configuración Avanzada</h4>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="olt-snmp" class="block text-sm font-medium text-gray-700 mb-1">Comunidad SNMP</label>
                    <input type="text" id="olt-snmp" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="public">
                </div>
                <div>
                    <label for="olt-port" class="block text-sm font-medium text-gray-700 mb-1">Puerto SSH</label>
                    <input type="number" id="olt-port" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="22">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="olt-telnet" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="olt-telnet" class="ml-2 block text-sm text-gray-700">Habilitar Telnet</label>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm transition-colors duration-200">
                Cancelar
            </button>
            <button class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                Guardar Configuración
            </button>
        </div>
    </div>
</div>
