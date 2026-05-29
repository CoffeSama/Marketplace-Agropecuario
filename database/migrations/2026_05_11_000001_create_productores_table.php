<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_productor', 100);
            $table->string('nombre_finca', 255);
            $table->string('ubicacion_administrativa', 255);
            $table->unsignedTinyInteger('años_experiencia')->default(0);
            $table->string('documento_identidad', 50);
            $table->string('archivo_documento')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productores');
    }
};
