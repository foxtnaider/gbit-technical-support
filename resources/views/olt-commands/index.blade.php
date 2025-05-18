<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comandos OLT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ejecutar Comandos en OLT</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">Seleccionar OLT</h4>
                            
                            <div class="mb-4">
                                <label for="olt-select" class="block text-sm font-medium text-gray-700 mb-1">OLT:</label>
                                <select id="olt-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Selecciona una OLT</option>
                                    @forelse ($olts as $olt)
                                        <option value="{{ $olt->id }}" data-ip="{{ $olt->ip_address }}" data-username="{{ $olt->username }}" data-password="{{ $olt->password }}">
                                            {{ $olt->olt_name ?: ($olt->brand . ' ' . $olt->model ?: 'OLT #'.$olt->id) }} ({{ $olt->ip_address ?: 'Sin IP' }})
                                        </option>
                                    @empty
                                        <option value="" disabled>No hay OLTs disponibles en el sistema</option>
                                    @endforelse
                                </select>
                                
                                <!-- Información de depuración -->
                                @if(config('app.debug'))
                                <div class="mt-2 p-2 bg-gray-100 rounded text-xs">
                                    <p>Total OLTs: {{ $olts->count() }}</p>
                                    <p>Tipos disponibles: {{ $olts->pluck('type')->unique()->implode(', ') ?: 'Ninguno' }}</p>
                                </div>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <label for="command-select" class="block text-sm font-medium text-gray-700 mb-1">Comando predefinido:</label>
                                <select id="command-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Selecciona un comando</option>
                                    @foreach ($commonCommands as $command)
                                        <option value="{{ $command }}">{{ $command }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="custom-command" class="block text-sm font-medium text-gray-700 mb-1">Comando personalizado:</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" id="custom-command" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Ingresa un comando personalizado">
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button id="execute-command" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Ejecutar Comando
                                </button>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">Resultado del Comando</h4>
                            
                            <div id="command-loading" class="hidden">
                                <div class="flex items-center justify-center p-4">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Ejecutando comando...</span>
                                </div>
                            </div>
                            
                            <div id="command-error" class="hidden bg-red-50 p-4 rounded-md">
                                <!-- El mensaje de error se insertará aquí -->
                            </div>
                            
                            <div id="command-result" class="bg-black text-green-400 p-4 rounded-md h-96 min-h-96 overflow-y-auto font-mono text-sm">
                                <p class="text-gray-500 italic">El resultado del comando se mostrará aquí.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historial de Comandos</h3>
                    
                    <div id="command-history" class="bg-gray-50 p-4 rounded-md h-48 overflow-y-auto">
                        <p class="text-gray-500 italic">No hay comandos ejecutados recientemente.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del DOM
        const oltSelect = document.getElementById('olt-select');
        const commandSelect = document.getElementById('command-select');
        const customCommand = document.getElementById('custom-command');
        const executeButton = document.getElementById('execute-command');
        const commandResult = document.getElementById('command-result');
        const commandLoading = document.getElementById('command-loading');
        const commandError = document.getElementById('command-error');
        const commandHistory = document.getElementById('command-history');
        
        // Variables para almacenar la información de la OLT seleccionada
        let selectedOltId = null;
        let sessionId = null;
        
        // Evento para seleccionar un comando predefinido
        commandSelect.addEventListener('change', function() {
            if (this.value) {
                customCommand.value = this.value;
            }
        });
        
        // Evento para seleccionar una OLT
        oltSelect.addEventListener('change', function() {
            selectedOltId = this.value;
        });
        
        // Evento para ejecutar un comando (botón)
        executeButton.addEventListener('click', function() {
            executeOltCommand();
        });
        
        // Evento para ejecutar un comando (presionar Enter en el campo de comando)
        customCommand.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                executeOltCommand();
            }
        });
        
        // Función para ejecutar un comando en la OLT
        async function executeOltCommand() {
            // Verificar que se ha seleccionado una OLT
            if (!selectedOltId) {
                showError('Por favor, selecciona una OLT primero.');
                return;
            }
            
            // Obtener el comando a ejecutar
            const command = customCommand.value.trim();
            if (!command) {
                showError('Por favor, ingresa un comando para ejecutar.');
                return;
            }
            
            // Mostrar indicador de carga
            commandResult.innerHTML = '';
            commandLoading.classList.remove('hidden');
            commandError.classList.add('hidden');
            
            try {
                // Enviar solicitud al servidor
                const response = await fetch('{{ route("olt-commands.execute") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        olt_id: selectedOltId,
                        command: command
                    })
                });
                
                const data = await response.json();
                
                // Ocultar indicador de carga
                commandLoading.classList.add('hidden');
                
                if (data.success) {
                    // Mostrar resultado del comando
                    displayCommandResult(command, data);
                    
                    // Agregar al historial
                    addToCommandHistory(command, data.olt);
                } else {
                    // Mostrar error
                    showError(data.message || 'Error al ejecutar el comando.');
                }
            } catch (error) {
                console.error('Error al ejecutar comando:', error);
                commandLoading.classList.add('hidden');
                showError('Error de conexión: ' + error.message);
            }
        }
        
        // Función para mostrar errores
        function showError(message) {
            commandError.innerHTML = `<p class="text-sm text-red-600">${message}</p>`;
            commandError.classList.remove('hidden');
        }
        
        // Función para mostrar el resultado del comando
        function displayCommandResult(command, data) {
            let resultHtml = `<p class="text-green-600 font-bold">$ ${command}</p>`;
            
            if (data.output && typeof data.output === 'string') {
                // Formatear la salida del comando con saltos de línea
                const formattedOutput = data.output
                    .replace(/\n/g, '<br>')
                    .replace(/\s/g, '&nbsp;');
                
                resultHtml += `<pre class="whitespace-pre-wrap mt-2">${formattedOutput}</pre>`;
            } else {
                resultHtml += `<p class="text-gray-500 mt-2">El comando se ejecutó correctamente pero no devolvió ninguna salida.</p>`;
            }
            
            commandResult.innerHTML = resultHtml;
        }
        
        // Función para agregar un comando al historial
        function addToCommandHistory(command, olt) {
            // Eliminar el mensaje de "No hay comandos" si existe
            if (commandHistory.querySelector('.text-gray-500.italic')) {
                commandHistory.innerHTML = '';
            }
            
            const timestamp = new Date().toLocaleTimeString();
            const historyItem = document.createElement('div');
            historyItem.className = 'flex justify-between items-center py-1 border-b border-gray-200 last:border-0';
            historyItem.innerHTML = `
                <div class="flex flex-col">
                    <span class="text-sm font-mono">${command}</span>
                    <span class="text-xs text-gray-600">OLT: ${olt.name} (${olt.ip})</span>
                </div>
                <span class="text-xs text-gray-500">${timestamp}</span>
            `;
            
            // Agregar evento de clic para volver a ejecutar el comando
            historyItem.addEventListener('click', function() {
                // Seleccionar la OLT correspondiente
                for (let i = 0; i < oltSelect.options.length; i++) {
                    if (oltSelect.options[i].value == olt.id) {
                        oltSelect.selectedIndex = i;
                        selectedOltId = olt.id;
                        break;
                    }
                }
                
                customCommand.value = command;
                customCommand.focus();
            });
            
            // Agregar al principio del historial
            commandHistory.insertBefore(historyItem, commandHistory.firstChild);
        }
    });
    </script>
</x-app-layout>
