<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productor extends Model
{
    protected $table = 'productores';

    protected $fillable = [
        'user_id',
        'tipo_productor',
        'nombre_finca',
        'ubicacion_administrativa',
        'años_experiencia',
        'documento_identidad',
        'archivo_documento',
        'latitud',
        'longitud',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
