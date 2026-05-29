<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    protected $table = 'transportistas';

    protected $fillable = [
        'user_id',
        'tipo_transporte',
        'capacidad_carga',
        'zona_operacion',
        'licencia_conducir',
        'placa_vehiculo',
        'archivo_documento',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
