<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nap extends Model
{
    use HasFactory;

    protected $fillable = [
        'nap_number',
        'name',
        'description',
        'status',
        'brand',
        'model',
        'installation_date',
        'address',
        'latitude',
        'longitude',
        'total_ports',
        'available_ports',
        'connector_type',
        'network_device_id',
        'pon_number'
    ];

    protected $casts = [
        'installation_date' => 'date',
        'total_ports' => 'integer',
        'available_ports' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
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
}
