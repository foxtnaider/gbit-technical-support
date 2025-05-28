<?php

namespace App\Http\Controllers;

use App\Models\NetworkDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            // Construir la URL base de la API externa
            $apiBaseUrl = $this->getApiBaseUrl();
            
            // Preparar los datos para la petición
            $data = [
                'sessionId' => $request->sessionId,
                'command' => $request->command,
            ];
            
            // Agregar configMode si está presente
            if ($request->has('configMode')) {
                // $data['configMode'] = $request->configMode;
                $data['configMode'] = true;
            }
            
            // Realizar la petición a la API externa
            $response = Http::post("{$apiBaseUrl}/api/olt/send-command", $data);
            
            // Verificar si hay un error específico de sesión inactiva
            $responseData = $response->json();
            if (isset($responseData['success']) && $responseData['success'] === false && 
                isset($responseData['message']) && $responseData['message'] === 'Error al enviar comando: No hay una sesión activa') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar comando: No hay una sesión activa'
                ]);
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
