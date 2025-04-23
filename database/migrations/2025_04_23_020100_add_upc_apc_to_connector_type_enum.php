<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, creamos una columna temporal para almacenar los valores actuales
        Schema::table('naps', function (Blueprint $table) {
            $table->string('connector_type_temp')->nullable();
        });

        // Copiamos los valores actuales a la columna temporal
        DB::statement('UPDATE naps SET connector_type_temp = connector_type');

        // Eliminamos la columna original con el tipo enum
        Schema::table('naps', function (Blueprint $table) {
            $table->dropColumn('connector_type');
        });

        // Creamos una nueva columna con tipo string
        Schema::table('naps', function (Blueprint $table) {
            $table->string('connector_type')->default('SC');
        });

        // Copiamos los valores de vuelta a la nueva columna
        DB::statement('UPDATE naps SET connector_type = connector_type_temp');

        // Eliminamos la columna temporal
        Schema::table('naps', function (Blueprint $table) {
            $table->dropColumn('connector_type_temp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero, creamos una columna temporal para almacenar los valores actuales
        Schema::table('naps', function (Blueprint $table) {
            $table->string('connector_type_temp')->nullable();
        });

        // Copiamos los valores actuales a la columna temporal
        DB::statement('UPDATE naps SET connector_type_temp = connector_type');

        // Eliminamos la columna string
        Schema::table('naps', function (Blueprint $table) {
            $table->dropColumn('connector_type');
        });

        // Creamos una nueva columna con tipo enum
        Schema::table('naps', function (Blueprint $table) {
            $table->enum('connector_type', ['SC', 'LC', 'FC', 'ST', 'MPO', 'MTP', 'Other', 'UPC', 'APC'])->default('SC');
        });

        // Copiamos los valores de vuelta a la nueva columna
        DB::statement('UPDATE naps SET connector_type = connector_type_temp');

        // Eliminamos la columna temporal
        Schema::table('naps', function (Blueprint $table) {
            $table->dropColumn('connector_type_temp');
        });
    }
};
