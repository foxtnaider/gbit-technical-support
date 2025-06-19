<div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-xl font-semibold mb-4">Comandos Rápidos</h3>
    
    <div class="mb-6">
        <label for="olt-select" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar OLT</label>
        <select id="olt-select" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            <option value="">Seleccione una OLT</option>
            <option value="1">OLT Principal - 192.168.1.1</option>
            <option value="2">OLT Secundaria - 192.168.1.2</option>
            <option value="3">OLT Respaldo - 192.168.1.3</option>
        </select>
    </div>
    
    <div class="space-y-4">
        <div class="p-4 border border-gray-200 rounded-lg">
            <h4 class="font-medium mb-2">Comandos Comunes</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <button class="bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-md text-sm transition-colors duration-200">
                    show onu status all
                </button>
                <button class="bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-md text-sm transition-colors duration-200">
                    show sys mem
                </button>
                <button class="bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-md text-sm transition-colors duration-200">
                    show running-config
                </button>
                <button class="bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-md text-sm transition-colors duration-200">
                    show onu opm-diag all
                </button>
            </div>
        </div>
        
        <div class="p-4 border border-gray-200 rounded-lg">
            <h4 class="font-medium mb-2">Terminal</h4>
            <div class="bg-gray-900 text-gray-100 p-3 rounded-md font-mono text-sm h-64 overflow-y-auto">
                <p class="opacity-70">$ Conectado a OLT</p>
                <p class="opacity-70">$ Ingrese un comando...</p>
                <p class="opacity-100 mt-1 border-l-2 border-blue-400 pl-2">_</p>
            </div>
        </div>
    </div>
</div>
