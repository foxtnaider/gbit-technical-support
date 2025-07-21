<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NetworkDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OltCommandController extends Controller
{
    /**
     * Muestra la vista principal para ejecutar comandos en OLTs
     */
    public function index()
    {
        // Obtener todas las OLTs activas (considerando posibles variaciones en el campo type)
        $olts = NetworkDevice::where(function($query) {
                                $query->where('type', 'olt')
                                      ->orWhere('type', 'OLT')
                                      ->orWhereNotNull('olt_name');
                            })
                            ->orderBy('id')
                            ->get();

        // Obtener la cantidad de OLTs activas (status 'active' y no eliminadas)
        $oltsActivas = NetworkDevice::where(function($query) {
            $query->where('type', 'olt')
                  ->orWhere('type', 'OLT');
        })
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->count();
        
        // Registrar la cantidad de OLTs encontradas para depuración
        Log::info('OLTs encontradas para la vista de comandos: ' . $olts->count());
        
        // Si no hay OLTs, registrar todos los dispositivos para depuración
        if ($olts->isEmpty()) {
            $allDevices = NetworkDevice::all();
            Log::info('Total de dispositivos en la base de datos: ' . $allDevices->count());
            Log::info('Tipos de dispositivos: ' . $allDevices->pluck('type')->unique()->implode(', '));
        }
        
        // Lista de comandos comunes predefinidos
        $commonCommands = [
            "show onu status all",
            "show sys mem",
            "show running-config",
            "show onu opm-diag all",
            "show mac address-table",
            "show mac address-table interface gpon 0/1",
            "show pon onu state interface gpon 0/1",
            "show onu state 1",
            "show top",
            "list",
            "show onu allowlist",
            "show pon onu state interface gpon 1/4",
            "show interface"
        ];
        
        return view('olt-commands.index', compact('olts', 'commonCommands', 'oltsActivas'));
    }
    
    /**
     * Ejecuta un comando en la OLT seleccionada
     */
    public function executeCommand(Request $request)
    {
        Log::info('Iniciando ejecución de comando OLT', [
            'comando' => $request->command,
            'olt_id' => $request->olt_id
        ]);
        
        $request->validate([
            'olt_id' => 'required|exists:network_devices,id',
            'command' => 'required|string'
        ]);
        
        try {
            // Obtener la OLT seleccionada
            $olt = NetworkDevice::findOrFail($request->olt_id);
            Log::info('OLT encontrada', [
                'olt_id' => $olt->id,
                'olt_name' => $olt->olt_name,
                'ip' => $olt->ip_address
            ]);
            
            // Verificar que tenemos la información necesaria
            if (empty($olt->ip_address) || empty($olt->username) || empty($olt->password)) {
                Log::warning('Faltan credenciales para la OLT', ['olt_id' => $olt->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan credenciales para conectarse a la OLT'
                ]);
            }
            
            // Obtener la URL de la API OLT desde las variables de entorno
            $apiOltHost = env('API_OLT_HOST', '167.86.100.118');
            $apiOltPort = env('API_OLT_PORT', '3000');
            $apiOltUrl = "http://{$apiOltHost}:{$apiOltPort}";
            
            Log::info("Intentando conectar a la API OLT", [
                'url' => $apiOltUrl,
                'ip_olt' => $olt->ip_address,
                'username' => $olt->username
            ]);
            
            // Conectar a la OLT (autenticación) - Enviando credenciales en los headers
            try {
                Log::info('Intentando conectar a la API OLT con estas credenciales', [
                    'ip' => $olt->ip_address,
                    'username' => $olt->username,
                    // No loguear la contraseña por seguridad
                    'api_url' => "{$apiOltUrl}/api/connect"
                ]);
                
                $connectResponse = Http::timeout(20)
                    ->withHeaders([
                        'IP-Address' => $olt->ip_address,
                        'Username' => $olt->username,
                        'Password' => $olt->password,
                        'Content-Type' => 'application/json'
                    ])
                    ->post("{$apiOltUrl}/api/connect");
            } catch (\Exception $e) {
                Log::error('Excepción al conectar con la API OLT', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error de conexión con la API OLT: ' . $e->getMessage()
                ]);
            }
            
            Log::info('Respuesta de conexión recibida', [
                'status' => $connectResponse->status(),
                'successful' => $connectResponse->successful(),
                'body' => $connectResponse->body(),
                'json' => $connectResponse->json()
            ]);
            
            if (!$connectResponse->successful() || !$connectResponse->json('success')) {
                Log::error('Error al conectar con la OLT', [
                    'status' => $connectResponse->status(),
                    'body' => $connectResponse->body(),
                    'error' => $connectResponse->json('error') ?? 'Error desconocido'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo establecer conexión con la OLT: ' . ($connectResponse->json('error') ?? 'Error desconocido')
                ]);
            }
            
            $sessionId = $connectResponse->json('sessionId');
            Log::info('Sesión establecida con la OLT', ['sessionId' => $sessionId]);
            
            // Preparar los comandos a ejecutar
            // Siguiendo el flujo correcto según la documentación:
            // 1. Primero enviamos 'configure terminal'
            // 2. Luego enviamos el comando específico
        
            // Primero enviamos el comando 'configure terminal'
            try {
                $configResponse = Http::timeout(20)
                    ->withHeaders([
                        'IP-Address' => $olt->ip_address,
                        'Username' => $olt->username,
                        'Password' => $olt->password,
                        'Content-Type' => 'application/json'
                    ])
                    ->post("{$apiOltUrl}/api/privileged-commands", [
                        'sessionId' => $sessionId,
                        'commands' => ['configure terminal']
                    ]);
                
                Log::info('Respuesta del comando configure terminal', [
                    'status' => $configResponse->status(),
                    'successful' => $configResponse->successful(),
                    'body' => $configResponse->body()
                ]);
                
                if (!$configResponse->successful() || !$configResponse->json('success')) {
                    Log::warning('Error al ejecutar configure terminal, continuando con el comando principal', [
                        'error' => $configResponse->json('error') ?? 'Error desconocido'
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Excepción al ejecutar configure terminal, continuando con el comando principal', [
                    'message' => $e->getMessage()
                ]);
            }
        
            // Ahora preparamos el comando principal solicitado por el usuario
            $commands = [$request->command];
            
            Log::info('Enviando comandos a la OLT', [
                'sessionId' => $sessionId,
                'commands' => $commands,
                'command_count' => count($commands)
            ]);
            
            // Ejecutar el comando en modo privilegiado
            try {
                $commandResponse = Http::timeout(30)
                    ->withHeaders([
                        'IP-Address' => $olt->ip_address,
                        'Username' => $olt->username,
                        'Password' => $olt->password,
                        'Content-Type' => 'application/json'
                    ])
                    ->post("{$apiOltUrl}/api/privileged-commands", [
                        'sessionId' => $sessionId,
                        'commands' => $commands
                    ]);
            } catch (\Exception $e) {
                Log::error('Excepción al enviar comandos a la OLT', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'sessionId' => $sessionId,
                    'commands' => $commands
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar comandos a la OLT: ' . $e->getMessage()
                ]);
            }
            
            Log::info('Respuesta de comando recibida', [
                'status' => $commandResponse->status(),
                'successful' => $commandResponse->successful(),
                'body' => $commandResponse->body(),
                'json' => $commandResponse->json()
            ]);
            
            if (!$commandResponse->successful() || !$commandResponse->json('success')) {
                Log::error('Error al ejecutar comando en la OLT', [
                    'status' => $commandResponse->status(),
                    'body' => $commandResponse->body(),
                    'error' => $commandResponse->json('error') ?? 'Error desconocido'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al ejecutar el comando: ' . ($commandResponse->json('error') ?? 'Error desconocido')
                ]);
            }
            
            // Analizar la respuesta del comando
            $output = $commandResponse->json('output');
            
            // Procesar la salida según su estructura
            $processedOutput = '';
            if (is_array($output)) {
                // Si output es un array, verificamos si tiene la clave 'raw' o 'lines'
                if (isset($output['raw'])) {
                    $processedOutput = $output['raw'];
                } elseif (isset($output['lines']) && is_array($output['lines'])) {
                    $processedOutput = implode("\n", $output['lines']);
                } else {
                    // Si no tiene ninguna de esas claves, lo convertimos a JSON
                    $processedOutput = json_encode($output, JSON_PRETTY_PRINT);
                }
            } else {
                // Si no es un array, lo usamos directamente
                $processedOutput = $output ?? '';
            }
            
            Log::info('Comando ejecutado correctamente', [
                'command' => $request->command,
                'output_type' => gettype($output),
                'output_structure' => is_array($output) ? array_keys($output) : 'no es array',
                'processed_output_length' => strlen($processedOutput)
            ]);
            
            // Verificación específica para el comando 'show mac address-table'
            if (strpos($request->command, 'show mac address-table') !== false && empty($processedOutput)) {
                Log::warning('El comando "show mac address-table" no devolvió datos', [
                    'command' => $request->command,
                    'raw_response' => $commandResponse->body()
                ]);
            }
            
            // Guardar el comando en el historial (opcional)
            // Aquí podrías implementar un modelo para guardar el historial de comandos
            
            return response()->json([
                'success' => true,
                'output' => $processedOutput,
                'command' => $request->command,
                'olt' => [
                    'id' => $olt->id,
                    'name' => $olt->olt_name ?: ($olt->brand . ' ' . $olt->model),
                    'ip' => $olt->ip_address
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Excepción al ejecutar comando en OLT', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'command' => $request->command ?? 'N/A',
                'olt_id' => $request->olt_id ?? 'N/A'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtiene las estadísticas de ONUs desde la API externa
     */
    public function getOnuStatistics()
    {
        try {
            // Intentar obtener datos del caché primero (con TTL de 5 minutos)
            if (Cache::has('onu_statistics')) {
                return response()->json(Cache::get('onu_statistics'));
            }
            
            // Obtener la URL base de la API desde las variables de entorno
            $apiBaseUrl = env('API_TRUNK_OLT_INTERNAL');
            
            if (empty($apiBaseUrl)) {
                Log::error('La variable de entorno API_TRUNK_OLT no está configurada');
                return response()->json([
                    'success' => false,
                    'message' => 'Error de configuración: API_TRUNK_OLT no está definida'
                ], 500);
            }
            
            $apiUrl = rtrim($apiBaseUrl, '/') . '/api/statistics/summary';
            
            Log::info('Consultando estadísticas de ONUs', ['url' => $apiUrl]);
            
            // Realizar la petición a la API externa
            $response = Http::timeout(15)->get($apiUrl);
            
            if (!$response->successful()) {
                Log::error('Error al obtener estadísticas de ONUs', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener estadísticas: ' . $response->status()
                ], $response->status());
            }
            
            // Procesar la respuesta con la nueva estructura
            $apiData = $response->json();
            
            // Transformar la estructura de datos para mantener compatibilidad con el frontend
            $transformedData = [
                'success' => $apiData['success'] ?? false,
                'statistics' => [
                    'totals' => [
                        'total' => $apiData['data']['totalOnus'] ?? 0,
                        'working' => $apiData['data']['connectedOnus'] ?? 0,
                        'offline' => $apiData['data']['disconnectedOnus'] ?? 0,
                        'dyinggasp' => $apiData['data']['dyingGaspOnus'] ?? 0
                    ],
                    'olts' => []
                ]
            ];
            
            // Transformar los datos de cada OLT
            if (!empty($apiData['data']['oltsData'])) {
                foreach ($apiData['data']['oltsData'] as $olt) {
                    $transformedData['statistics']['olts'][] = [
                        'id' => $olt['oltId'] ?? null,
                        'name' => $olt['oltName'] ?? 'OLT Sin Nombre',
                        'total' => $olt['totalOnus'] ?? 0,
                        'working' => $olt['connectedOnus'] ?? 0,
                        'offline' => $olt['disconnectedOnus'] ?? 0,
                        'dyinggasp' => $olt['dyingGaspOnus'] ?? 0,
                        'status' => $olt['status'] ?? 'unknown',
                        'error' => $olt['error'] ?? null
                    ];
                }
            }
            
            // Guardar en caché por 5 minutos
            Cache::put('onu_statistics', $transformedData, now()->addMinutes(5));
            
            Log::info('Estadísticas de ONUs obtenidas correctamente', [
                'olts_count' => count($transformedData['statistics']['olts'] ?? []),
                'total_onus' => $transformedData['statistics']['totals']['total'] ?? 0
            ]);
            
            return response()->json($transformedData);
            
        } catch (\Exception $e) {
            Log::error('Excepción al obtener estadísticas de ONUs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
