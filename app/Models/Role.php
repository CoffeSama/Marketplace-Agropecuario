<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    const ADMIN        = 'admin';
    const PRODUCTOR    = 'productor';
    const COMPRADOR    = 'comprador';
    const TRANSPORTISTA = 'transportista';

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', $nombre);
    }
}
