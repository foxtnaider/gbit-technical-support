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
        Schema::create('onts', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->enum('status', ['active', 'inactive', 'defective'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onts');
    }
};
