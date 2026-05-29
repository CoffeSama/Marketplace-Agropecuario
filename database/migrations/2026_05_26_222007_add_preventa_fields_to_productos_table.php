<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->date('fecha_disponibilidad')->nullable()->after('descripcion');
            $table->enum('estado_disponibilidad', ['disponible', 'preventa'])
                ->default('disponible')
                ->after('fecha_disponibilidad');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['fecha_disponibilidad', 'estado_disponibilidad']);
        });
    }
};