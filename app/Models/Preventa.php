<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preventa extends Model
{
    protected $fillable = [
        'producto_id',
        'comprador_id',
        'cantidad',
        'total',
        'anticipo',
        'saldo',
        'estado',
        'fecha_disponibilidad',
    ];

    protected $casts = [
        'fecha_disponibilidad' => 'date',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }
}