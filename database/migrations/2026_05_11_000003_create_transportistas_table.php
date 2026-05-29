<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transportistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_transporte', 100);
            $table->string('capacidad_carga', 100);
            $table->string('zona_operacion', 255);
            $table->string('licencia_conducir', 50);
            $table->string('placa_vehiculo', 20);
            $table->string('archivo_documento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transportistas');
    }
};
