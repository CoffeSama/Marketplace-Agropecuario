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
        'fecha_disponibilidad',
        'estado_disponibilidad',
        'estado',
    ];

    protected $casts = [
        'fecha_disponibilidad' => 'date',
    ];

    public function productor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class);
    }

    public function preventas()
    {
        return $this->hasMany(Preventa::class);
    }
}