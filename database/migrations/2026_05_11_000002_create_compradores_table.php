<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compradores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_comprador', 100);
            $table->string('zona_compra', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compradores');
    }
};
