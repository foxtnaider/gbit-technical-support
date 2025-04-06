<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'ont_id',
        'nap_id',
        'nap_port',
        'service_plan',
        'installation_date',
        'status',
        'observations',
    ];

    protected $casts = [
        'installation_date' => 'date',
    ];

    /**
     * Obtiene el cliente asociado a esta conexión.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Obtiene la ONT asociada a esta conexión.
     */
    public function ont()
    {
        return $this->belongsTo(Ont::class);
    }

    /**
     * Obtiene la NAP asociada a esta conexión.
     */
    public function nap()
    {
        return $this->belongsTo(Nap::class);
    }

    /**
     * Obtiene el estado formateado para mostrar.
     */
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'suspended' => 'Suspendido',
            'pending' => 'Pendiente',
            default => $this->status,
        };
    }
}
