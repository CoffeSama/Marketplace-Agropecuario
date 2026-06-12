<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'comprador_id',
        'productor_id',
        'total',
        'estado',
        'visto_productor',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'visto_productor' => 'boolean',
    ];

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function productor()
    {
        return $this->belongsTo(User::class, 'productor_id');
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
