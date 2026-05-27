<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preventas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->foreignId('comprador_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('cantidad', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('anticipo', 10, 2);
            $table->decimal('saldo', 10, 2);

            $table->enum('estado', [
                'pendiente_saldo',
                'completado',
                'cancelado'
            ])->default('pendiente_saldo');

            $table->date('fecha_disponibilidad');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventas');
    }
};