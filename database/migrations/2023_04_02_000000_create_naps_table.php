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
        Schema::create('naps', function (Blueprint $table) {
            $table->id();
            $table->string('nap_number')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->date('installation_date')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('total_ports');
            $table->integer('available_ports');
            $table->enum('connector_type', ['SC', 'LC', 'FC', 'ST', 'MPO', 'MTP', 'Other'])->default('SC');
            $table->foreignId('network_device_id')->constrained('network_devices')->onDelete('cascade');
            $table->string('pon_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naps');
    }
};
