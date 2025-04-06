<?php

namespace App\Http\Controllers;

use App\Jobs\PingNetworkDeviceJob;
use App\Models\NetworkDevice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    /**
     * Muestra la interfaz de monitoreo
     */
    public function index()
    {
        // Obtener todos los dispositivos OLT (insensible a mayúsculas/minúsculas)
        $devices = NetworkDevice::whereRaw('LOWER(type) = ?', ['olt'])
            ->orderBy('last_checked_at', 'desc')
            ->get();
            
        // Obtener información sobre los trabajos en la cola
        $pendingJobs = DB::table('jobs')->count();
        
        // Obtener los últimos logs relacionados con el monitoreo
        $logPath = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logPath)) {
            $logContent = file_get_contents($logPath);
            $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?(Verificación de OLT|dispositivos enviados para verificación|no es una OLT).*?(\n|$)/';
            
            if (preg_match_all($pattern, $logContent, $matches)) {
                $logs = array_slice($matches[0], -20); // Obtener los últimos 20 logs relacionados
            }
        }
        
        return view('monitoring.index', compact('devices', 'pendingJobs', 'logs'));
    }
    
    /**
     * Ejecuta la verificación de estado para todos los dispositivos OLT
     */
    public function runCheck()
    {
        try {
            Artisan::call('devices:check-status');
            $output = Artisan::output();
            
            return redirect()->route('monitoring.index')
                ->with('success', 'Verificación iniciada correctamente. ' . $output);
        } catch (\Exception $e) {
            Log::error('Error al ejecutar la verificación de dispositivos: ' . $e->getMessage());
            
            return redirect()->route('monitoring.index')
                ->with('error', 'Error al iniciar la verificación: ' . $e->getMessage());
        }
    }
    
    /**
     * Ejecuta la verificación de estado para un dispositivo específico
     */
    public function checkDevice($id)
    {
        try {
            $device = NetworkDevice::findOrFail($id);
            
            if (strtolower($device->type) !== 'olt') {
                return redirect()->route('monitoring.index')
                    ->with('error', 'Solo se pueden verificar dispositivos OLT');
            }
            
            PingNetworkDeviceJob::dispatch($device);
            
            return redirect()->route('monitoring.index')
                ->with('success', "Verificación iniciada para OLT {$device->id}: {$device->ip_address}");
        } catch (\Exception $e) {
            Log::error('Error al verificar dispositivo: ' . $e->getMessage());
            
            return redirect()->route('monitoring.index')
                ->with('error', 'Error al verificar dispositivo: ' . $e->getMessage());
        }
    }
    
    /**
     * Limpia la cola de trabajos pendientes
     */
    public function clearQueue()
    {
        try {
            DB::table('jobs')->delete();
            
            return redirect()->route('monitoring.index')
                ->with('success', 'Cola de trabajos limpiada correctamente');
        } catch (\Exception $e) {
            Log::error('Error al limpiar la cola de trabajos: ' . $e->getMessage());
            
            return redirect()->route('monitoring.index')
                ->with('error', 'Error al limpiar la cola de trabajos: ' . $e->getMessage());
        }
    }
    
    /**
     * Reinicia el worker de colas
     */
    public function restartWorker()
    {
        try {
            // Esto solo funciona si el worker se está ejecutando con supervisord
            // En un entorno de desarrollo, tendrás que reiniciar el worker manualmente
            Artisan::call('queue:restart');
            
            return redirect()->route('monitoring.index')
                ->with('success', 'Worker reiniciado correctamente. Los trabajadores se reiniciarán después de terminar su trabajo actual.');
        } catch (\Exception $e) {
            Log::error('Error al reiniciar el worker: ' . $e->getMessage());
            
            return redirect()->route('monitoring.index')
                ->with('error', 'Error al reiniciar el worker: ' . $e->getMessage());
        }
    }
}
