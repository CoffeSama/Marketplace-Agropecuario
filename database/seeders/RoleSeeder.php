<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'admin',         'descripcion' => 'Administrador con control total del sistema'],
            ['nombre' => 'productor',     'descripcion' => 'Productor que publica productos agropecuarios'],
            ['nombre' => 'comprador',     'descripcion' => 'Comprador que adquiere productos en el marketplace'],
            ['nombre' => 'transportista', 'descripcion' => 'Transportista que ofrece servicios de logística'],
        ];

        foreach ($roles as $rol) {
            Role::updateOrCreate(['nombre' => $rol['nombre']], $rol);
        }

        $compradorRole = Role::where('nombre', 'comprador')->first();
        if ($compradorRole) {
            User::whereNull('role_id')->update(['role_id' => $compradorRole->id]);
        }

        $this->command->info('✅ Roles creados: admin, productor, comprador, transportista');
    }
}
