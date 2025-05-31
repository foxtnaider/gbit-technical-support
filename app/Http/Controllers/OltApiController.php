<?php

namespace App\Http\Controllers;

use App\Models\NetworkDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CommandLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class OltApiController extends Controller
{
    /**
     * Muestra la página principal de la API OLT con la lista de dispositivos de red.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener todos los dispositivos de red (OLTs) con los campos necesarios
        // Incluyendo la contraseña para autocompletado
        $devices = NetworkDevice::select('id', 'olt_name', 'password', 'ip_address', 'port')
                                ->where(function($query) {
                                    $query->where('type', 'like', '%olt%')
                                            ->orWhere('type', 'like', '%OLT%');
                                })
                                ->get()
                                ->map(function($device) {
                                   // Asegurarse de que el nombre se muestre correctamente
                                    $device->display_name = $device->olt_name ?: 'OLT ' . $device->id;
                                    if ($device->ip_address) {
                                        $device->display_name .= ' (' . $device->ip_address;
                                        if ($device->port) {
                                            $device->display_name .= ':' . $device->port;
                                        }
                                        $device->display_name .= ')';
                                    }
                                    return $device;
                                });
        
        // Si no se encontraron dispositivos, obtener todos los dispositivos de red
        if ($devices->isEmpty()) {
            $devices = NetworkDevice::select('id', 'olt_name', 'password', 'ip_address', 'port')
                                    ->get()
                                    ->map(function($device) {
                                        $device->display_name = $device->olt_name ?: 'OLT ' . $device->id;
                                        if ($device->ip_address) {
                                            $device->display_name .= ' (' . $device->ip_address;
                                            if ($device->port) {
                                                $device->display_name .= ':' . $device->port;
                                            }
                                            $device->display_name .= ')';
                                        }
                                        return $device;
                                    });
        }
        
        return view('olt-api.index', ['devices' => $devices]);
    }

    /**
     * Construye la URL base para la API OLT a partir de la configuración.
     *
     * @return string
     */
    private function getApiBaseUrl()
    {
        $host = config('olt-api.host');
        $port = config('olt-api.port');
        
        return "http://{$host}:{$port}";
    }

    /**
     * Registra un comando en el historial de comandos
     *
     * @param string $command Comando a registrar
     * @return void
     */
    private function logCommand($command)
    {
        $commandLogs = Session::get('command_logs', []);
        array_unshift($commandLogs, [
            'command' => $command,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        // Mantener solo los últimos 100 comandos
        $commandLogs = array_slice($commandLogs, 0, 100);
        
        Session::put('command_logs', $commandLogs);
    }
    
    /**
     * Obtiene el último comando enviado
     * 
     * @return string|null
     */
    private function getLastCommand()
    {
        $commandLogs = Session::get('command_logs', []);
        return $commandLogs[0]['command'] ?? null;
    }
    
    /**
     * Formatea la respuesta de la tabla MAC
     * 
     * @param array $data Datos de la tabla MAC (array de entradas)
     * @return string HTML con la tabla formateada
     */
    private function formatMacTable($data)
    {
        if (!is_array($data) || empty($data)) {
            return "<div class='p-4 text-red-600'>No se encontraron direcciones MAC en la tabla.</div>";
        }
        
        // Iniciar la tabla
        $table = "<div class='overflow-x-auto mt-4 bg-white rounded-lg shadow overflow-y-auto' style='max-height: 70vh;'>";
        $table .= "<table class='min-w-full divide-y divide-gray-200'>";
        
        // Encabezados de la tabla
        $table .= "<thead class='bg-gray-50 sticky top-0'>";
        $table .= "<tr>";
        $table .= "<th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>VLAN</th>";
        $table .= "<th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Dirección MAC</th>";
        $table .= "<th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Tipo</th>";
        $table .= "<th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Puerto</th>";
        $table .= "<th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Estado</th>";
        $table .= "</tr>";
        $table .= "</thead>";
        
        // Cuerpo de la tabla
        $table .= "<tbody class='bg-white divide-y divide-gray-200'>";
        
        // Contador para el total de entradas
        $total = 0;
        
        // Filas de datos
        foreach ($data as $entry) {
            if (is_array($entry)) {
                // Obtener los valores con claves en minúsculas para mayor consistencia
                $entry = array_change_key_case($entry, CASE_LOWER);
                
                $vlan = $entry['vlan'] ?? '';
                $mac = $entry['macaddress'] ?? $entry['mac'] ?? '';
                $type = $entry['type'] ?? '';
                $port = $entry['port'] ?? $entry['ports'] ?? '';
                $state = $entry['state'] ?? 'Aging';
                
                // Formatear la MAC para mostrar con dos puntos cada dos caracteres
                if (!empty($mac)) {
                    $mac = implode(':', str_split(str_replace([':', '-', '.'], '', $mac), 2));
                    $mac = strtoupper($mac);
                }
                
                $table .= "<tr class='hover:bg-gray-50'>";
                $table .= "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>$vlan</td>";
                $table .= "<td class='px-6 py-4 whitespace-nowrap text-sm font-mono text-blue-600'>$mac</td>";
                $table .= "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$type</td>";
                $table .= "<td class='px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900'>$port</td>";
                $table .= "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$state</td>";
                $table .= "</tr>";
                
                $total++;
            }
        }
        
        $table .= "</tbody>";
        
        // Pie de tabla con contador
        $table .= "<tfoot class='bg-gray-50'>";
        $table .= "<tr>";
        $table .= "<td colspan='5' class='px-6 py-2 text-right text-xs text-gray-500'>";
        $table .= "Total de direcciones: $total";
        $table .= "</td>";
        $table .= "</tr>";
        $table .= "</tfoot>";
        
        $table .= "</table>";
        $table .= "</div>";
        
        return $table;
    }

    /**
     * Establece una conexión con un dispositivo OLT a través de la API externa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function performConnect(Request $request)
    {
        Log::info('Iniciando conexión a OLT', [
            'device_id' => $request->device_id,
            'has_enable_password' => !empty($request->enablePassword)
        ]);

        $request->validate([
            'device_id' => 'required|exists:network_devices,id',
            'enablePassword' => 'required|string',
        ]);

        try {
            Log::debug('Validaciones pasadas correctamente');
            
            // Obtener el dispositivo de red seleccionado
            $device = NetworkDevice::findOrFail($request->device_id);
            
            Log::debug('Dispositivo encontrado', [
                'id' => $device->id,
                'name' => $device->name,
                'ip' => $device->ip_address,
                'port' => $device->port,
                'username' => $device->username ? '***' : 'no definido',
                'password' => $device->password ? '***' : 'no definido'
            ]);
            
            // Construir la URL base de la API externa
            $apiBaseUrl = $this->getApiBaseUrl();
            $apiUrl = "{$apiBaseUrl}/api/olt/connect";
            
            Log::debug('Preparando solicitud a API externa', [
                'api_url' => $apiUrl,
                'ip' => $device->ip_address,
                'port' => $device->port,
                'username' => $device->username ? '***' : 'no definido',
                'has_password' => !empty($device->password),
                'has_enable_password' => !empty($request->enablePassword)
            ]);
            
            // Realizar la petición a la API externa
            $response = Http::timeout(30)->post($apiUrl, [
                'ip' => $device->ip_address,
                'port' => $device->port,
                'username' => $device->username,
                'password' => $device->password,
                'enablePassword' => $request->enablePassword,
            ]);
            
            $responseData = $response->json();
            
            Log::info('Respuesta de la API OLT', [
                'status_code' => $response->status(),
                'response' => $responseData,
                'success' => $response->successful()
            ]);
            
            // Devolver la respuesta de la API externa
            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Error al conectar con OLT: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al conectar con la OLT: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envía un comando a un dispositivo OLT a través de la API externa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function performSendCommand(Request $request)
    {
        $request->validate([
            'sessionId' => 'required|string',
            'command' => 'required|string',
            'configMode' => 'boolean',
        ]);

        try {
            // Registrar el comando
            $this->logCommand($request->command);
            
            // Construir la URL base de la API externa
            $apiBaseUrl = $this->getApiBaseUrl();
            
            // Preparar los datos para la petición
            $data = [
                'sessionId' => $request->sessionId,
                'command' => $request->command,
            ];
            
            // Agregar configMode si está presente
            if ($request->has('configMode')) {
                $data['configMode'] = true;
            }
            
            // Realizar la petición a la API externa
            $response = Http::post("{$apiBaseUrl}/api/olt/send-command", $data);
            
            // Verificar si hay un error específico de sesión inactiva
            $responseData = $response->json();
            
            if (isset($responseData['success']) && $responseData['success'] === false) {
                if (isset($responseData['message']) && $responseData['message'] === 'Error al enviar comando: No hay una sesión activa') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al enviar comando: No hay una sesión activa'
                    ]);
                }
                return response()->json($responseData);
            }
            
            // Verificar si el comando es para mostrar la tabla MAC y formatear la respuesta
            if (stripos($request->command, 'show mac address-table') !== false) {
                // Usar la propiedad 'data' de la respuesta para generar la tabla
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $responseData['formatted'] = $this->formatMacTable($responseData['data']);
                } else {
                    // Si no hay datos, devolver un mensaje de error
                    $responseData['formatted'] = '<div class="p-4 text-red-600">No se encontraron datos en la tabla MAC.</div>';
                }
            }
            
            // Devolver la respuesta de la API externa
            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Error al enviar comando a OLT: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar comando a la OLT: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desconecta de un dispositivo OLT a través de la API externa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function performDisconnect(Request $request)
    {
        $request->validate([
            'sessionId' => 'required|string',
        ]);

        try {
            // Construir la URL base de la API externa
            $apiBaseUrl = $this->getApiBaseUrl();
            
            // Realizar la petición a la API externa
            $response = Http::post("{$apiBaseUrl}/api/olt/disconnect", [
                'sessionId' => $request->sessionId,
            ]);
            
            // Devolver la respuesta de la API externa
            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Error al desconectar de OLT: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al desconectar de la OLT: ' . $e->getMessage()
            ], 500);
        }
    }
}
