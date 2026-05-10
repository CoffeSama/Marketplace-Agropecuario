<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono')->nullable()->after('email');
            $table->enum('estado', ['registrado', 'pendiente_verificacion', 'verificado'])->default('registrado')->after('telefono');
            $table->boolean('terminos_aceptados')->default(false)->after('estado');
            $table->string('archivo_documento')->nullable()->after('terminos_aceptados');

            // Productor
            $table->string('tipo_productor')->nullable()->after('archivo_documento');
            $table->string('nombre_finca')->nullable();
            $table->string('ubicacion_administrativa')->nullable();
            $table->unsignedTinyInteger('años_experiencia')->nullable();
            $table->string('documento_identidad')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Comprador
            $table->string('tipo_comprador')->nullable();
            $table->string('zona_compra')->nullable();

            // Transportista
            $table->string('tipo_transporte')->nullable();
            $table->string('capacidad_carga')->nullable();
            $table->string('zona_operacion')->nullable();
            $table->string('licencia_conducir')->nullable();
            $table->string('placa_vehiculo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telefono', 'estado', 'terminos_aceptados', 'archivo_documento',
                'tipo_productor', 'nombre_finca', 'ubicacion_administrativa',
                'años_experiencia', 'documento_identidad', 'latitud', 'longitud',
                'tipo_comprador', 'zona_compra',
                'tipo_transporte', 'capacidad_carga', 'zona_operacion',
                'licencia_conducir', 'placa_vehiculo',
            ]);
        });
    }
};
