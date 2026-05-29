<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprador extends Model
{
    protected $fillable = [
        'user_id',
        'tipo_comprador',
        'zona_compra',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
