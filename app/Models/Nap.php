<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nap extends Model
{
    use HasFactory;

    protected $fillable = [
        'nap_number',
        'description',
        'status',
        'installation_date',
        'address',
        'latitude',
        'longitude',
        'total_ports',
        'available_ports',
        'connector_type',
        'network_device_id',
        'pon_number',
        'reference_power',
        'fdt_distance'
    ];

    protected $casts = [
        'installation_date' => 'date',
        'total_ports' => 'integer',
        'available_ports' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'reference_power' => 'float',
        'fdt_distance' => 'float',
    ];

    /**
     * Obtiene el dispositivo de red (OLT) asociado a esta NAP.
     */
    public function networkDevice()
    {
        return $this->belongsTo(NetworkDevice::class);
    }

    /**
     * Obtiene el estado formateado para mostrar.
     */
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'maintenance' => 'En Mantenimiento',
            default => $this->status,
        };
    }
    
    /**
     * Verifica si un PON específico ya está asignado a otra NAP para la misma OLT.
     *
     * @param int $networkDeviceId ID del dispositivo de red (OLT)
     * @param string $ponNumber Número de PON
     * @param int|null $excludeNapId ID de la NAP a excluir de la verificación (útil para ediciones)
     * @return bool
     */
    public static function isPonAssigned($networkDeviceId, $ponNumber, $excludeNapId = null)
    {
        $query = self::where('network_device_id', $networkDeviceId)
                    ->where('pon_number', $ponNumber);
                    
        if ($excludeNapId) {
            $query->where('id', '!=', $excludeNapId);
        }
        
        return $query->exists();
    }
}
