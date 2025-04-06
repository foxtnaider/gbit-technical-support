<?php

namespace App\Jobs;

use App\Models\NetworkDevice;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PingNetworkDeviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El dispositivo de red a verificar
     *
     * @var NetworkDevice
     */
    protected $networkDevice;

    /**
     * Número máximo de intentos de ping
     *
     * @var int
     */
    protected $maxAttempts = 3;

    /**
     * Tiempo de espera para cada intento de ping (en segundos)
     *
     * @var int
     */
    protected $timeout = 1;

    /**
     * Pausa entre intentos fallidos (en segundos)
     *
     * @var int
     */
    protected $pauseBetweenAttempts = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(NetworkDevice $networkDevice)
    {
        $this->networkDevice = $networkDevice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Verificar si es una OLT y tiene dirección IP
        if (strtolower($this->networkDevice->type) !== 'olt' || empty($this->networkDevice->ip_address)) {
            Log::info("Dispositivo {$this->networkDevice->id} no es una OLT o no tiene IP, omitiendo verificación");
            return;
        }

        $ipAddress = $this->networkDevice->ip_address;
        $pingStatus = 'inaccesible'; // Por defecto asumimos que está inaccesible
        
        // Realizar los intentos de ping
        for ($attempt = 1; $attempt <= $this->maxAttempts; $attempt++) {
            $result = $this->executePing($ipAddress);
            
            if ($result) {
                $pingStatus = 'accesible';
                break; // Si el ping fue exitoso, no necesitamos más intentos
            }
            
            // Si el ping falló y no es el último intento, esperamos antes del siguiente intento
            if ($attempt < $this->maxAttempts) {
                sleep($this->pauseBetweenAttempts);
            }
        }
        
        // Actualizar el registro del dispositivo en la base de datos
        $this->updateDeviceStatus($pingStatus);
        
        Log::info("Verificación de OLT {$this->networkDevice->id} ({$this->networkDevice->ip_address}): {$pingStatus}");
    }
    
    /**
     * Ejecuta el comando ping y devuelve true si fue exitoso, false si falló
     *
     * @param string $ipAddress
     * @return bool
     */
    protected function executePing(string $ipAddress): bool
    {
        // Construir el comando ping con timeout
        $command = "ping -c 1 -W {$this->timeout} {$ipAddress} > /dev/null 2>&1";
        
        // Ejecutar el comando y obtener el código de salida
        exec($command, $output, $exitCode);
        
        // Si el código de salida es 0, el ping fue exitoso
        return $exitCode === 0;
    }
    
    /**
     * Actualiza el estado del dispositivo en la base de datos
     *
     * @param string $pingStatus
     * @return void
     */
    protected function updateDeviceStatus(string $pingStatus): void
    {
        $this->networkDevice->last_checked_at = Carbon::now();
        $this->networkDevice->last_ping_status = $pingStatus;
        
        // Opcionalmente, también podríamos actualizar el campo status general
        // pero solo si no está en mantenimiento
        if ($this->networkDevice->status !== 'maintenance') {
            $this->networkDevice->status = ($pingStatus === 'accesible') ? 'active' : 'inactive';
        }
        
        $this->networkDevice->save();
    }
}
