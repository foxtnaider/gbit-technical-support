<?php

namespace App\Console\Commands;

use App\Jobs\PingNetworkDeviceJob;
use App\Models\NetworkDevice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDeviceStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica el estado de conectividad de los dispositivos OLT mediante ping';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificación de estado de dispositivos OLT...');
        
        // Obtener todas las OLTs con dirección IP que no estén en mantenimiento
        $devices = NetworkDevice::whereRaw('LOWER(type) = ?', ['olt'])
            ->whereNotNull('ip_address')
            ->where(function ($query) {
                $query->where('status', '!=', 'maintenance')
                      ->orWhereNull('status');
            })
            ->get();
            
        $count = $devices->count();
        
        if ($count === 0) {
            $this->warn('No se encontraron dispositivos OLT con dirección IP para verificar.');
            return 0;
        }
        
        $this->info("Se encontraron {$count} dispositivos OLT para verificar.");
        
        // Enviar cada dispositivo a la cola para su verificación
        foreach ($devices as $device) {
            $this->line("Enviando verificación para OLT {$device->id}: {$device->ip_address}");
            PingNetworkDeviceJob::dispatch($device);
        }
        
        $this->info('Todas las verificaciones han sido enviadas a la cola.');
        Log::info("Comando devices:check-status ejecutado. {$count} dispositivos enviados para verificación.");
        
        return 0;
    }
}
