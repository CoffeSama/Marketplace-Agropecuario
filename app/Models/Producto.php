<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'categoria',
        'precio',
        'cantidad_disponible',
        'unidad_medida',
        'descripcion',
        'estado',
    ];

    public function productor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class);
    }
}