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
            $table->integer('max_onts_per_pon')->nullable()->after('pon_number')->comment('Capacidad máxima de ONTs por PON');
            $table->string('pon_types_supported')->nullable()->after('max_onts_per_pon')->comment('Tipos de PON soportados (GPON, EPON, etc.)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_devices', function (Blueprint $table) {
            $table->dropColumn('max_onts_per_pon');
            $table->dropColumn('pon_types_supported');
        });
    }
};
