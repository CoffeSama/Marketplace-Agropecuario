<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comprador_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('productor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('total', 12, 2);

            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'pagado'])
                ->default('pendiente');

            // Notificación visual: el productor aún no vio el pedido
            $table->boolean('visto_productor')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
