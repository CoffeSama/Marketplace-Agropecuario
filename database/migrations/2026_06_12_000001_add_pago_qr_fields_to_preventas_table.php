<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventas', function (Blueprint $table) {
            $table->boolean('anticipo_pagado')->default(true)->after('anticipo');
            $table->boolean('saldo_pagado')->default(false)->after('saldo');
        });
    }

    public function down(): void
    {
        Schema::table('preventas', function (Blueprint $table) {
            $table->dropColumn(['anticipo_pagado', 'saldo_pagado']);
        });
    }
};
