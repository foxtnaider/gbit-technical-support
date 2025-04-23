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
            $table->dropColumn(['name', 'brand', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naps', function (Blueprint $table) {
            $table->string('name')->nullable()->after('nap_number');
            $table->string('brand')->nullable()->after('status');
            $table->string('model')->nullable()->after('brand');
        });
    }
};
