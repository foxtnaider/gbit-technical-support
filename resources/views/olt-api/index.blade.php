@extends('layouts.olt-api')

@section('content')
<div class="container py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gbit-blue-800 mb-6">API OLT</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="device-select" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Dispositivo OLT</label>
                        <select id="device-select" class="input-field w-full">
                            <option value="">Seleccione un dispositivo...</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}">{{ $device->name ?? $device->olt_name ?? 'OLT ' . $device->id }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">Dispositivos encontrados: {{ count($devices) }}</p>
                    </div>
                    <div>
                        <label for="enable-password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña de elevación de privilegios</label>
                        <input type="password" id="enable-password" class="input-field w-full" placeholder="Ingrese la contraseña de elevación">
                    </div>
                </div>

                <div class="flex space-x-4 mb-6">
                    <button id="connect-btn" class="btn-primary flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Conectar
                    </button>
                    <button id="disconnect-btn" class="btn-secondary flex items-center opacity-50 cursor-not-allowed" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M13 10V8a4 4 0 00-8 0v2H3a1 1 0 00-1 1v6a1 1 0 001 1h14a1 1 0 001-1v-6a1 1 0 00-1-1h-2zm-5-2a2 2 0 114 0v2H8V8zm-3 4h10v4H5v-4z" clip-rule="evenodd" />
                        </svg>
                        Desconectar
                    </button>
                </div>

                <div class="mb-6">
                    <label for="command-input" class="block text-sm font-medium text-gray-700 mb-2">Comando</label>
                    <div class="flex">
                        <input type="text" id="command-input" class="input-field flex-grow" placeholder="Ingrese un comando..." disabled>
                        <button id="send-command-btn" class="btn-primary ml-2 opacity-50 cursor-not-allowed" disabled>
                            Enviar
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="response-area" class="block text-sm font-medium text-gray-700 mb-2">Respuesta</label>
                    <textarea id="response-area" class="input-field w-full font-mono text-sm" rows="15" readonly></textarea>
                </div>

                <div id="status-area" class="bg-blue-50 text-blue-700 p-4 rounded-md mb-4 hidden"></div>
                <div id="error-area" class="bg-red-50 text-red-700 p-4 rounded-md mb-4 hidden"></div>

                <!-- Campo oculto para almacenar el sessionId -->
                <input type="hidden" id="session-id" value="">
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a elementos del DOM
        const deviceSelect = document.getElementById('device-select');
        const enablePasswordInput = document.getElementById('enable-password');
        const connectBtn = document.getElementById('connect-btn');
        const disconnectBtn = document.getElementById('disconnect-btn');
        const commandInput = document.getElementById('command-input');
        const sendCommandBtn = document.getElementById('send-command-btn');
        const responseArea = document.getElementById('response-area');
        const statusArea = document.getElementById('status-area');
        const errorArea = document.getElementById('error-area');
        const sessionIdInput = document.getElementById('session-id');

        // Variables para almacenar datos de conexión
        let connectionData = {
            device_id: null,
            enablePassword: null
        };

        // Función para mostrar mensajes de estado
        function showStatus(message, isError = false) {
            if (isError) {
                errorArea.textContent = message;
                errorArea.classList.remove('d-none');
                statusArea.classList.add('d-none');
            } else {
                statusArea.textContent = message;
                statusArea.classList.remove('d-none');
                errorArea.classList.add('d-none');
            }
        }

        // Función para limpiar mensajes
        function clearMessages() {
            statusArea.classList.add('d-none');
            errorArea.classList.add('d-none');
        }

        // Función para actualizar la UI según el estado de conexión
        function updateUIConnectionState(isConnected) {
            deviceSelect.disabled = isConnected;
            enablePasswordInput.disabled = isConnected;
            connectBtn.disabled = isConnected;
            disconnectBtn.disabled = !isConnected;
            commandInput.disabled = !isConnected;
            sendCommandBtn.disabled = !isConnected;
            
            if (isConnected) {
                showStatus('Conectado a la OLT');
            } else {
                showStatus('Desconectado de la OLT');
                sessionIdInput.value = '';
            }
        }

        // Función para conectar a la OLT
        async function connectToOLT() {
            clearMessages();
            
            const device_id = deviceSelect.value;
            const enablePassword = enablePasswordInput.value;
            
            if (!device_id) {
                showStatus('Por favor, seleccione un dispositivo OLT', true);
                return;
            }
            
            if (!enablePassword) {
                showStatus('Por favor, ingrese la contraseña de elevación de privilegios', true);
                return;
            }
            
            showStatus('Conectando a la OLT...');
            
            try {
                const response = await fetch('/api/olt/perform-connect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ device_id, enablePassword })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Almacenar sessionId
                    sessionIdInput.value = data.sessionId;
                    
                    // Almacenar datos de conexión para posible reconexión
                    connectionData = {
                        device_id,
                        enablePassword
                    };
                    
                    // Actualizar UI
                    updateUIConnectionState(true);
                    responseArea.value = 'Conexión establecida correctamente.\n';
                } else {
                    showStatus('Error al conectar: ' + data.message, true);
                }
            } catch (error) {
                showStatus('Error de conexión: ' + error.message, true);
            }
        }

        // Función para desconectar de la OLT
        async function disconnectFromOLT() {
            clearMessages();
            
            const sessionId = sessionIdInput.value;
            
            if (!sessionId) {
                showStatus('No hay una sesión activa', true);
                return;
            }
            
            showStatus('Desconectando de la OLT...');
            
            try {
                const response = await fetch('/api/olt/perform-disconnect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sessionId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Limpiar datos de conexión
                    connectionData = {
                        device_id: null,
                        enablePassword: null
                    };
                    
                    // Actualizar UI
                    updateUIConnectionState(false);
                    responseArea.value += 'Desconexión exitosa.\n';
                } else {
                    showStatus('Error al desconectar: ' + data.message, true);
                }
            } catch (error) {
                showStatus('Error de desconexión: ' + error.message, true);
            }
        }

        // Función para enviar un comando a la OLT
        async function sendCommand(command = null, isRetry = false) {
            if (!isRetry) {
                clearMessages();
            }
            
            const sessionId = sessionIdInput.value;
            const cmd = command || commandInput.value;
            
            if (!sessionId) {
                showStatus('No hay una sesión activa', true);
                return;
            }
            
            if (!cmd) {
                showStatus('Por favor, ingrese un comando', true);
                return;
            }
            
            if (!isRetry) {
                showStatus('Enviando comando...');
                responseArea.value += `\n> ${cmd}\n`;
            }
            
            try {
                const response = await fetch('/api/olt/perform-send-command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sessionId, command: cmd })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    responseArea.value += data.response + '\n';
                    if (!isRetry) {
                        commandInput.value = '';
                    }
                    clearMessages();
                } else {
                    // Verificar si es el error específico de sesión inactiva
                    if (data.message === 'Error al enviar comando: No hay una sesión activa') {
                        showStatus('Sesión perdida. Intentando reconectar...', true);
                        
                        // Intentar reconexión automática
                        await reconnectAndRetryCommand(cmd);
                    } else {
                        showStatus('Error al enviar comando: ' + data.message, true);
                    }
                }
            } catch (error) {
                showStatus('Error al enviar comando: ' + error.message, true);
            }
        }

        // Función para reconectar y reintentar el comando
        async function reconnectAndRetryCommand(originalCommand) {
            // Verificar si tenemos los datos de conexión almacenados
            if (!connectionData.device_id || !connectionData.enablePassword) {
                showStatus('No se puede reconectar: datos de conexión no disponibles', true);
                return;
            }
            
            try {
                // Desconectar la sesión antigua (opcional)
                const oldSessionId = sessionIdInput.value;
                if (oldSessionId) {
                    try {
                        await fetch('/api/olt/perform-disconnect', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ sessionId: oldSessionId })
                        });
                    } catch (error) {
                        // Ignorar errores en la desconexión
                    }
                }
                
                // Intentar reconexión
                const reconnectResponse = await fetch('/api/olt/perform-connect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        device_id: connectionData.device_id,
                        enablePassword: connectionData.enablePassword
                    })
                });
                
                const reconnectData = await reconnectResponse.json();
                
                if (reconnectData.success) {
                    // Actualizar sessionId con el nuevo
                    sessionIdInput.value = reconnectData.sessionId;
                    
                    showStatus('Reconexión exitosa. Reintentando comando...');
                    responseArea.value += 'Reconexión exitosa. Reintentando comando...\n';
                    
                    // Reintentar el comando original
                    await sendCommand(originalCommand, true);
                } else {
                    showStatus('Error en la reconexión: ' + reconnectData.message, true);
                }
            } catch (error) {
                showStatus('Error en la reconexión: ' + error.message, true);
            }
        }

        // Event listeners
        connectBtn.addEventListener('click', connectToOLT);
        disconnectBtn.addEventListener('click', disconnectFromOLT);
        sendCommandBtn.addEventListener('click', () => sendCommand());
        
        // Permitir enviar comando con Enter
        commandInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendCommand();
            }
        });
    });
</script>
@endsection
