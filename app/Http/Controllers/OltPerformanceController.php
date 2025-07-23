<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class OltPerformanceController extends Controller
{
    /**
     * Muestra la página de rendimiento de OLT/ONU.
     */
    public function index()
    {
        $apiUrl = env('API_TRUNK_OLT_INTERNAL') . '/api/olt-statistics';
        $olts = [];
        $error = null;

        try {
            // Usamos timeout para evitar esperas largas
            $response = Http::timeout(30)->get($apiUrl);

            if ($response->successful()) {
                $olts = $this->processOltData($response->json());
            } else {
                $error = 'Error al conectar con la API de estadísticas: ' . $response->status();
                Log::error($error, ['url' => $apiUrl, 'status' => $response->status()]);
            }
        } catch (\Exception $e) {
            $error = 'No se pudo establecer conexión con el servicio de estadísticas. Por favor, inténtelo más tarde.';
            Log::error($error, ['url' => $apiUrl, 'exception' => $e->getMessage()]);
        }

        return view('olt-performance.index', compact('olts', 'error'));
    }

    /**
     * Procesa los datos de la OLT para añadir estadísticas y clasificar la potencia.
     */
    private function processOltData(array $olts): array
    {
        return array_map(function ($olt) {
            // Inicializar con todos los estados posibles
            $overallStats = [
                'ok' => 0, 
                'warning' => 0, 
                'critical' => 0, 
                'high_power_warning' => 0,
                'offline' => 0,
                'total' => 0
            ];
            $ponPortsData = [];

            if (!empty($olt['powerOnuStatistics'])) {
                foreach ($olt['powerOnuStatistics'] as $onu) {
                    $pon = $onu['pon'];

                    // Inicializar el puerto si no existe
                    if (!isset($ponPortsData[$pon])) {
                        $ponPortsData[$pon] = [
                            'onus' => [],
                            'stats' => [
                                'ok' => 0, 
                                'warning' => 0, 
                                'critical' => 0, 
                                'high_power_warning' => 0,
                                'offline' => 0,
                                'total' => 0
                            ]
                        ];
                    }

                    // Procesar y clasificar la ONU
                    $powerStatus = $this->getPowerStatus($onu['rxPower']);
                    $onu['powerStatus'] = $powerStatus;

                    if ($powerStatus !== 'unknown') {
                        $ponPortsData[$pon]['stats'][$powerStatus]++;
                        $ponPortsData[$pon]['stats']['total']++;
                        $overallStats[$powerStatus]++;
                        $overallStats['total']++;
                    }
                    
                    $ponPortsData[$pon]['onus'][] = $onu;
                }
            }
            
            ksort($ponPortsData);

            $olt['processedStats'] = $overallStats;
            $olt['ponPortsData'] = $ponPortsData;
            $olt['overallStatus'] = $this->getOverallStatus($overallStats);

            return $olt;
        }, $olts);
    }

    /**
     * Determina el estado de la potencia de una ONU.
     */
    private function getPowerStatus($rxPower): string
{
    // Log para ver el valor de entrada
    Log::info("Depuración getPowerStatus: rxPower de entrada = " . $rxPower);

    // 1. Manejar el estado 'offline'
    if ($rxPower == 0) {
        Log::info("Depuración getPowerStatus: rxPower = 0, retornando 'offline'");
        return 'offline';
    }
    // 2. Manejar potencias demasiado altas (saturación)
    if ($rxPower >= -10) {
        Log::info("Depuración getPowerStatus: rxPower >= -10, retornando 'high_power_warning'");
        return 'high_power_warning';
    }
    // 3. Manejar potencias críticas (extremadamente bajas)
    if ($rxPower < -27) {
        Log::info("Depuración getPowerStatus: rxPower < -27, retornando 'critical'");
        return 'critical';
    }
    // 4. Manejar potencias de advertencia (bajas, pero funcionales)
    if ($rxPower >= -27 && $rxPower < -24) {
        Log::info("Depuración getPowerStatus: rxPower entre -27 y -24, retornando 'warning'");
        return 'warning';
    }
    // 5. Manejar potencias óptimas ('ok')
    if ($rxPower >= -24 && $rxPower < -10) {
        Log::info("Depuración getPowerStatus: rxPower entre -24 y -10, retornando 'ok'");
        return 'ok';
    }

    // 6. Si el valor no cae en ninguna categoría
    Log::info("Depuración getPowerStatus: rxPower fuera de todos los rangos definidos, retornando 'unknown'");
    return 'unknown';
}

    /**
     * Determina el estado general de la OLT basado en las estadísticas de sus ONUs.
     */
    private function getOverallStatus(array $stats): string
    {
        // Prioridad de estados: critical > high_power_warning > warning > offline > ok
        if ($stats['critical'] > 0) {
            return 'critical';
        }
        if ($stats['high_power_warning'] > 0) {
            return 'high_power_warning';
        }
        if ($stats['warning'] > 0) {
            return 'warning';
        }
        if ($stats['offline'] > 0) {
            return 'offline';
        }
        return 'ok';
    }
}
