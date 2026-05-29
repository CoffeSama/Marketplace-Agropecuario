<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nombre', 120);
            $table->string('categoria', 80);
            $table->decimal('precio', 10, 2);
            $table->decimal('cantidad_disponible', 10, 2);
            $table->string('unidad_medida', 30);
            $table->text('descripcion');

            $table->enum('estado', ['publicado', 'oculto'])->default('publicado');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};