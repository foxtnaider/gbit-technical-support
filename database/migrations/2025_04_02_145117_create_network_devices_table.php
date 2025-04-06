<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('network_devices', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // OLT o NAP
            $table->string('brand'); // Marca
            $table->string('model'); // Modelo
            $table->integer('pon_number')->nullable(); // Número de PON
            $table->string('ip_address')->nullable(); // Dirección IP
            $table->string('port')->nullable(); // Puerto
            $table->string('associated_server')->nullable(); // Servidor asociado
            $table->decimal('latitude', 10, 7)->nullable(); // Latitud para coordenadas GPS
            $table->decimal('longitude', 10, 7)->nullable(); // Longitud para coordenadas GPS
            $table->text('description')->nullable(); // Descripción adicional
            $table->string('status')->default('active'); // Estado del dispositivo (activo, inactivo, mantenimiento)
            $table->timestamps();
            $table->softDeletes(); // Para eliminación lógica
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_devices');
    }
};
