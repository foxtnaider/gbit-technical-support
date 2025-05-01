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
        Schema::table('naps', function (Blueprint $table) {
            $table->decimal('reference_power', 8, 2)->nullable()->comment('Potencia de referencia en dBm');
            $table->decimal('fdt_distance', 10, 2)->nullable()->comment('Distancia desde FDT en metros');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naps', function (Blueprint $table) {
            $table->dropColumn('reference_power');
            $table->dropColumn('fdt_distance');
        });
    }
};
