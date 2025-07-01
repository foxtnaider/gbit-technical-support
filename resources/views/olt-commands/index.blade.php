<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comandos OLT') }}
        </h2>
    </x-slot>

    <div class="flex flex-col md:flex-row">
        <!-- Menú lateral fijo a la izquierda -->
        <div class="w-full md:w-72 bg-white shadow-md min-h-screen border-r border-gray-200">
            <ul>
                <li>
                    <button 
                        class="w-full text-left px-4 py-3 border-b border-gray-200 transition-colors duration-200 menu-item hover:bg-gray-50" 
                        data-target="dashboard"
                    >
                        Panel de Control
                    </button>
                </li>
                <li>
                    <button 
                        class="w-full text-left px-4 py-3 border-b border-gray-200 transition-colors duration-200 menu-item hover:bg-gray-50" 
                        data-target="commands"
                    >
                        Comandos Rápidos
                    </button>
                </li>
                <li>
                    <button 
                        class="w-full text-left px-4 py-3 border-b border-gray-200 transition-colors duration-200 menu-item hover:bg-gray-50 bg-blue-50" 
                        data-target="status"
                    >
                        Estado de ONUs
                    </button>
                </li>
                <li>
                    <button 
                        class="w-full text-left px-4 py-3 border-b border-gray-200 transition-colors duration-200 menu-item hover:bg-gray-50" 
                        data-target="configuration"
                    >
                        Configuración
                    </button>
                </li>
                <li>
                    <button 
                        class="w-full text-left px-4 py-3 border-b border-gray-200 transition-colors duration-200 menu-item hover:bg-gray-50" 
                        data-target="logs"
                    >
                        Registros
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- Área de contenido -->
        <div class="w-full p-6">
            <div id="dashboard" class="content-section hidden">
                @include('components.olt-menu.dashboard')
            </div>
            <div id="commands" class="content-section hidden">
                @include('components.olt-menu.commands')
            </div>
            <div id="status" class="content-section">
                @include('components.olt-menu.status', ['olts' => $olts])
            </div>
            <div id="configuration" class="content-section hidden">
                @include('components.olt-menu.configuration')
            </div>
            <div id="logs" class="content-section hidden">
                @include('components.olt-menu.logs')
            </div>
        </div>
    </div>
    

</x-app-layout>
