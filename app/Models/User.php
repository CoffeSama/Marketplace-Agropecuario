<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'telefono',
        'estado',
        'terminos_aceptados',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function solicitudesVendedor()
    {
        return $this->hasMany(SolicitudVendedor::class);
    }

    public function productor()
    {
        return $this->hasOne(Productor::class);
    }

    public function comprador()
    {
        return $this->hasOne(Comprador::class);
    }

    public function transportista()
    {
        return $this->hasOne(Transportista::class);
    }

    public function getRoleNameAttribute()
    {
        if ($this->role_id) {
            $roleRelation = $this->getRelationValue('role');
            if ($roleRelation instanceof Role) {
                return $roleRelation->nombre;
            }
            $role = Role::find($this->role_id);
            if ($role) {
                return $role->nombre;
            }
        }
        return 'comprador';
    }

    public function isAdmin(): bool
    {
        return $this->role_name === Role::ADMIN;
    }

    public function isProductor(): bool
    {
        return $this->role_name === Role::PRODUCTOR;
    }

    public function isComprador(): bool
    {
        return $this->role_name === Role::COMPRADOR;
    }

    public function isTransportista(): bool
    {
        return $this->role_name === Role::TRANSPORTISTA;
    }

    public function estaVerificado(): bool
    {
        return $this->estado === 'verificado';
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente_verificacion';
    }

    public function solicitudPendiente()
    {
        return $this->solicitudesVendedor()->where('estado', 'pendiente')->first();
    }
}
