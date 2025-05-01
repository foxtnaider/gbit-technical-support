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
            $table->string('port_type')->nullable()->after('port')->comment('Tipo de puerto (ssh, web, telnet)');
            $table->string('secondary_port')->nullable()->after('port_type')->comment('Puerto secundario para conectarse al dispositivo');
            $table->string('secondary_port_type')->nullable()->after('secondary_port')->comment('Tipo de puerto secundario (ssh, web, telnet)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_devices', function (Blueprint $table) {
            $table->dropColumn('port_type');
            $table->dropColumn('secondary_port');
            $table->dropColumn('secondary_port_type');
        });
    }
};
