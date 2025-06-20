<?php

namespace App\Models;

use App\Models\Nap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NetworkDevice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'brand',
        'model',
        'address',
        'pon_number',
        'max_onts_per_pon',
        'pon_types_supported',
        'ip_address',
        'port',
        'port_type',
        'secondary_port',
        'secondary_port_type',
        'associated_server',
        'username',
        'password',
        'latitude',
        'longitude',
        'description',
        'power_threshold_low',
        'power_threshold_high',
        'status',
        'registration_status',
        'last_checked_at',
        'last_ping_status',
        'olt_name',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'pon_number' => 'integer',
        'max_onts_per_pon' => 'integer',
        'last_checked_at' => 'datetime',
    ];

    /**
     * Obtener el tipo de dispositivo formateado (OLT o NAP)
     *
     * @return string
     */
    public function getFormattedTypeAttribute(): string
    {
        return strtoupper($this->type);
    }

    /**
     * Obtener la ubicación completa como un string formateado
     *
     * @return string|null
     */
    public function getLocationAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }
        
        return null;
    }
    
    /**
     * Obtiene las NAPs asociadas a este dispositivo de red (OLT).
     */
    public function naps()
    {
        return $this->hasMany(Nap::class);
    }
}
