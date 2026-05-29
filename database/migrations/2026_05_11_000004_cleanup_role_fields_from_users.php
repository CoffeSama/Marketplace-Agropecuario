<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_productor',
                'nombre_finca',
                'ubicacion_administrativa',
                'años_experiencia',
                'documento_identidad',
                'latitud',
                'longitud',
                'tipo_comprador',
                'zona_compra',
                'tipo_transporte',
                'capacidad_carga',
                'zona_operacion',
                'licencia_conducir',
                'placa_vehiculo',
                'archivo_documento',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tipo_productor')->nullable();
            $table->string('nombre_finca')->nullable();
            $table->string('ubicacion_administrativa')->nullable();
            $table->unsignedTinyInteger('años_experiencia')->nullable();
            $table->string('documento_identidad')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('tipo_comprador')->nullable();
            $table->string('zona_compra')->nullable();
            $table->string('tipo_transporte')->nullable();
            $table->string('capacidad_carga')->nullable();
            $table->string('zona_operacion')->nullable();
            $table->string('licencia_conducir')->nullable();
            $table->string('placa_vehiculo')->nullable();
            $table->string('archivo_documento')->nullable();
        });
    }
};
