<div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-xl font-semibold mb-4">Panel de Control</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h4 class="font-medium text-blue-700">OLTs Activas</h4>
            <p class="text-2xl font-bold">{{ $oltsActivas }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <h4 class="font-medium text-green-700">ONUs Conectadas</h4>
            <p class="text-2xl font-bold">1,245</p>
        </div>
        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
            <h4 class="font-medium text-amber-700">Alertas Activas</h4>
            <p class="text-2xl font-bold">3</p>
        </div>
    </div>
    <div class="mt-6">
        <h4 class="font-medium mb-2">Estado del Sistema</h4>
        <div class="h-64 bg-gray-50 rounded-lg border border-gray-200 p-4">
            <p class="text-gray-500">Gráfico de estado del sistema aquí</p>
        </div>
    </div>
</div>
