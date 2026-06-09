<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $fillable = [
        'comprador_id',
        'productor_id',
        'estado',
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
        return $this->hasMany(CarritoItem::class);
    }

    public function getTotalAttribute()
    {
        return $this->items->sum('subtotal');
    }
}