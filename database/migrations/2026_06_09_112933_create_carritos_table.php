<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carritos', function (Blueprint $table) {
            $table->id();

            // Usuario comprador autenticado
            $table->foreignId('comprador_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Usuario productor dueño de los productos del carrito
            $table->foreignId('productor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('estado', ['activo', 'confirmado', 'cancelado'])
                ->default('activo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carritos');
    }
};