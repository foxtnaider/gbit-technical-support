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
            $table->string('username')->nullable()->after('associated_server');
            $table->string('password')->nullable()->after('username');
            $table->decimal('power_threshold_low', 8, 2)->default(-8)->after('description');
            $table->decimal('power_threshold_high', 8, 2)->default(-27)->after('power_threshold_low');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_devices', function (Blueprint $table) {
            $table->dropColumn(['username', 'password', 'power_threshold_low', 'power_threshold_high']);
        });
    }
};
