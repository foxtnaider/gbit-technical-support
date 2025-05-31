@extends('layouts.olt-api')

@section('styles')
<style>
    /* Estilos para la tabla MAC */
    .mac-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
        font-size: 0.875rem;
    }
    
    .mac-table th, 
    .mac-table td {
        padding: 0.5rem 1rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .mac-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #6b7280;
    }
    
    .mac-table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .mac-address {
        font-family: monospace;
        color: #3b82f6;
    }
    
    .port {
        font-family: monospace;
    }
    
    /* Estilos para las respuestas de comandos */
    .command-response {
        margin: 1rem 0;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .command-prompt {
        padding: 0.5rem 1rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        font-family: monospace;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .command-output {
        padding: 1rem;
        background-color: white;
    }
    
    /* Estilos para el área de respuesta */
    #response-area {
        font-family: monospace;
        white-space: pre-wrap;
        background-color: #1e293b;
        color: #e2e8f0;
        padding: 1rem;
        border-radius: 0.375rem;
        min-height: 200px;
        max-height: 500px;
        overflow-y: auto;
    }
    
    /* Estilos para el contenedor de la tabla MAC */
    .table-container {
        max-height: 500px;
        overflow-y: auto;
        margin: 1rem 0;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gbit-blue-800 mb-6">API OLT</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="device-select" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Dispositivo OLT</label>
                        <select id="device-select" class="input-field w-full" data-devices='@json($devices)'>
                            <option value="">Seleccione un dispositivo...</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}" data-password="{{ $device->password ?? '' }}">
                                    {{ $device->display_name }}
                                </option>
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
                    <div class="flex mb-2">
                        <input type="text" id="command-input" class="input-field flex-grow" placeholder="Ingrese un comando..." disabled>
                        <button id="send-command-btn" class="btn-primary ml-2 opacity-50 cursor-not-allowed" disabled>
                            Enviar
                        </button>
                    </div>
                    <div id="privilege-buttons" class="hidden space-x-2">
                        <button id="elevate-privileges-btn" class="bg-gbit-orange-500 hover:bg-gbit-orange-600 text-white text-xs font-semibold py-1 px-2 rounded transition-colors duration-200">
                            Elevar Privilegios
                        </button>
                        <button id="config-mode-btn" class="bg-gbit-blue-500 hover:bg-gbit-blue-600 text-white text-xs font-semibold py-1 px-2 rounded transition-colors duration-200">
                            Modo Configuración
                        </button>
                        <button id="show-mac-table-btn" class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold py-1 px-2 rounded transition-colors duration-200">
                            Tabla MAC
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
            
            // Mostrar/ocultar botones de privilegios
            const privilegeButtons = document.getElementById('privilege-buttons');
            if (isConnected) {
                showStatus('Conectado a la OLT');
                privilegeButtons.classList.remove('hidden');
            } else {
                showStatus('Desconectado de la OLT');
                sessionIdInput.value = '';
                privilegeButtons.classList.add('hidden');
            }
        }

        // Función para conectar a la OLT
        async function connectToOLT() {
            console.log('Iniciando conexión a OLT...');
            clearMessages();
            
            const device_id = deviceSelect.value;
            const enablePassword = enablePasswordInput.value;
            
            console.log('Datos del formulario:', { device_id, has_enable_password: !!enablePassword });
            
            if (!device_id) {
                const errorMsg = 'Por favor, seleccione un dispositivo OLT';
                console.error(errorMsg);
                showStatus(errorMsg, true);
                return;
            }
            
            if (!enablePassword) {
                const errorMsg = 'Por favor, ingrese la contraseña de elevación de privilegios';
                console.error(errorMsg);
                showStatus(errorMsg, true);
                return;
            }
            
            showStatus('Conectando a la OLT...');
            
            try {
                const url = '/api/olt/perform-connect';
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const requestBody = JSON.stringify({ 
                    device_id, 
                    enablePassword 
                });
                
                console.log('Enviando solicitud a:', url);
                console.log('Cuerpo de la solicitud:', { device_id, has_enable_password: !!enablePassword });
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: requestBody
                });
                
                console.log('Respuesta recibida, estado:', response.status);
                
                const data = await response.json().catch(error => {
                    console.error('Error al analizar la respuesta JSON:', error);
                    throw new Error('La respuesta del servidor no es un JSON válido');
                });
                
                console.log('Datos de la respuesta:', data);
                
                if (data.success) {
                    console.log('Conexión exitosa, sessionId:', data.sessionId);
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
                    const errorMsg = 'Error al conectar: ' + (data.message || 'Error desconocido');
                    console.error(errorMsg, data);
                    showStatus(errorMsg, true);
                }
            } catch (error) {
                const errorMsg = 'Error de conexión: ' + (error.message || 'Error desconocido');
                console.error(errorMsg, error);
                showStatus(errorMsg, true);
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
            const isMacTableCommand = cmd.toLowerCase().includes('show mac address-table');
            
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
                // Mostrar siempre el comando en el área de respuestas
                responseArea.value += `\n> ${cmd}\n`;
            }
            
            try {
                const response = await fetch('/api/olt/perform-send-command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        sessionId, 
                        command: cmd,
                        configMode: cmd.toLowerCase().startsWith('configure terminal')
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showStatus('Comando ejecutado correctamente');
                    
                    // Mostrar la salida del comando
                    if (isMacTableCommand && data.formatted) {
                        // Si es la tabla MAC y tenemos una versión formateada, mostrarla
                        const responseContainer = document.createElement('div');
                        responseContainer.className = 'command-response';
                        responseContainer.innerHTML = `
                            <div class='command-output mt-2'>
                                ${data.formatted}
                            </div>
                        `;
                        
                        // Insertar antes del textarea
                        responseArea.parentNode.insertBefore(responseContainer, responseArea);
                        
                        // También mostrar la salida formateada en el área de texto
                        responseArea.value += '\n' + data.formatted.replace(/<[^>]*>?/gm, '') + '\n';
                    } 
                    
                    // Mostrar siempre la salida en texto plano si está disponible
                    if (data.output) {
                        responseArea.value += data.output + '\n';
                    }
                    
                    // Desplazarse al final del área de respuesta
                    responseArea.scrollTop = responseArea.scrollHeight;
                    
                    // Limpiar el campo de comando
                    if (!isRetry) {
                        commandInput.value = '';
                    }
                } else {
                    // Si hay un error de sesión, intentar reconectar y reenviar el comando
                    if (data.message && data.message.includes('No hay una sesión activa')) {
                        await reconnectAndRetryCommand(cmd);
                    } else {
                        const errorMsg = 'Error: ' + (data.message || 'Error desconocido');
                        showStatus(errorMsg, true);
                        responseArea.value += errorMsg + '\n';
                    }
                }
            } catch (error) {
                const errorMsg = 'Error de conexión: ' + (error.message || 'Error desconocido');
                showStatus(errorMsg, true);
                responseArea.value += errorMsg + '\n';
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

    // Función para manejar el botón de elevar privilegios
    document.getElementById('elevate-privileges-btn').addEventListener('click', function() {
        const commandInput = document.getElementById('command-input');
        commandInput.value = 'enable';
        commandInput.focus();
        
        // Mostrar mensaje de estado
        const statusArea = document.getElementById('status-area');
        statusArea.textContent = 'Comando "enable" listo para enviar. Haga clic en Enviar para ejecutarlo.';
        statusArea.classList.remove('hidden');
    });
    
    // Función para manejar el botón de modo configuración
    document.getElementById('config-mode-btn').addEventListener('click', function() {
        const commandInput = document.getElementById('command-input');
        commandInput.value = 'configure terminal';
        commandInput.focus();
        
        // Mostrar mensaje de estado
        const statusArea = document.getElementById('status-area');
        statusArea.textContent = 'Comando "configure terminal" listo para enviar. Haga clic en Enviar para ejecutarlo.';
        statusArea.classList.remove('hidden');
    });

    // Función para manejar el botón de tabla MAC
    document.getElementById('show-mac-table-btn').addEventListener('click', function() {
        const commandInput = document.getElementById('command-input');
        commandInput.value = 'show mac address-table';
        commandInput.focus();
        
        // Mostrar mensaje de estado
        const statusArea = document.getElementById('status-area');
        statusArea.textContent = 'Comando listo. Haga clic en Enviar para ver la tabla de direcciones MAC.';
        statusArea.classList.remove('hidden');
    });

    // Manejar el cambio de selección de dispositivo
    document.getElementById('device-select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const password = selectedOption.getAttribute('data-password');
        const passwordInput = document.getElementById('enable-password');
        
        if (password) {
            passwordInput.value = password;
            
            // Mostrar mensaje de estado
            const statusArea = document.getElementById('status-area');
            statusArea.textContent = 'Contraseña cargada automáticamente para el dispositivo seleccionado.';
            statusArea.classList.remove('hidden');
            
            // Ocultar el mensaje después de 3 segundos
            setTimeout(() => {
                statusArea.classList.add('hidden');
            }, 3000);
        }
    });
</script>
@endsection
