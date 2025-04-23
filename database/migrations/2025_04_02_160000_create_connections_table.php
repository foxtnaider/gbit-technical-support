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
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('ont_id')->constrained('onts')->onDelete('cascade');
            $table->foreignId('nap_id')->constrained('naps')->onDelete('cascade');
            $table->integer('nap_port');
            $table->string('service_plan');
            $table->date('installation_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active');
            $table->text('observations')->nullable();
            $table->timestamps();
            
            // Asegurar que un puerto de NAP solo puede ser usado una vez
            $table->unique(['nap_id', 'nap_port']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
