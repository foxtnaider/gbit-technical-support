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
        Schema::table('network_devices', function (Blueprint $table) {
            $table->timestamp('last_checked_at')->nullable()->comment('Fecha y hora del último intento de ping');
            $table->string('last_ping_status')->nullable()->default('desconocido')->comment('Resultado del último ping: accesible, inaccesible, error_ping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_devices', function (Blueprint $table) {
            $table->dropColumn(['last_checked_at', 'last_ping_status']);
        });
    }
};
