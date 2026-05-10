<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'ok'      => true,
        'service' => 'api',
        'time'    => now()->toDateTimeString(),
    ]);
});
