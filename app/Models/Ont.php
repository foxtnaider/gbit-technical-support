<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ont extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'model',
        'brand',
        'status',
    ];

    /**
     * Obtiene las conexiones asociadas a esta ONT.
     */
    public function connections()
    {
        return $this->hasMany(Connection::class);
    }

    /**
     * Obtiene los clientes asociados a esta ONT a través de las conexiones.
     */
    public function customers()
    {
        return $this->hasManyThrough(Customer::class, Connection::class, 'ont_id', 'id', 'id', 'customer_id');
    }
}
