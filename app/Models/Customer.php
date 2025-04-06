<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'identity_document',
        'phone_number',
        'email',
        'address',
        'latitude',
        'longitude',
        'observations',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Obtiene las conexiones asociadas a este cliente.
     */
    public function connections()
    {
        return $this->hasMany(Connection::class);
    }

    /**
     * Obtiene las ONTs asociadas a este cliente a través de las conexiones.
     */
    public function onts()
    {
        return $this->hasManyThrough(Ont::class, Connection::class);
    }
}
