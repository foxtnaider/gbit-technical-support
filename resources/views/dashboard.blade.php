<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gbit-blue-800 dark:text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gbit-blue-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gbit-blue-800 dark:text-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold">{{ __("Bienvenido al Sistema de Soporte Técnico de GBIT") }}</h3>
                        <img src="https://gbit.com.ve/web/assets/img/gbit-nbg.png" alt="GBIT Logo" class="h-10 w-auto" />
                    </div>
                    <p class="mb-4">{{ __("Has iniciado sesión correctamente.") }}</p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Tarjeta de Estadísticas -->
                        <div class="bg-white dark:bg-gbit-blue-700 p-4 rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gbit-blue-800 dark:text-white">Tickets Activos</h4>
                                <span class="text-gbit-orange-500 text-xl font-bold">0</span>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de Estadísticas -->
                        <div class="bg-white dark:bg-gbit-blue-700 p-4 rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gbit-blue-800 dark:text-white">Tickets Resueltos</h4>
                                <span class="text-gbit-orange-500 text-xl font-bold">0</span>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de Estadísticas -->
                        <div class="bg-white dark:bg-gbit-blue-700 p-4 rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gbit-blue-800 dark:text-white">Tiempo Promedio</h4>
                                <span class="text-gbit-orange-500 text-xl font-bold">0h</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Módulos Administrativos (solo visible para administradores) -->
                    @if(auth()->user()->isAdmin())
                    <div class="mt-8 p-4 bg-gbit-blue-50 dark:bg-gbit-blue-700 rounded-lg border-l-4 border-gbit-orange-500">
                        <h4 class="font-medium text-gbit-blue-800 dark:text-white mb-2">Módulos Administrativos</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 bg-gbit-orange-500 hover:bg-gbit-orange-400 text-white rounded-md transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                Gestión de Usuarios
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-8 p-4 bg-gray-50 dark:bg-gbit-blue-700 rounded-lg">
                        <h4 class="font-medium text-gbit-blue-800 dark:text-white mb-2">Acciones Rápidas</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="#" class="px-4 py-2 bg-gbit-blue-800 hover:bg-gbit-blue-700 text-white rounded-md transition">Nuevo Ticket</a>
                            <a href="#" class="px-4 py-2 bg-gbit-orange-500 hover:bg-gbit-orange-400 text-white rounded-md transition">Ver Tickets</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
