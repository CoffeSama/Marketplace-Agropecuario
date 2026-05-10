<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('nombre', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('❌ El rol de administrador no existe. Ejecuta primero: php artisan db:seed --class=RoleSeeder');
            return;
        }

        $adminExists = User::where('email', 'admin@agrovida.com')->exists();

        if (!$adminExists) {
            User::create([
                'name'    => 'Administrador',
                'email'   => 'admin@agrovida.com',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'estado'  => 'verificado',
            ]);

            $this->command->info('✅ Usuario administrador creado exitosamente!');
            $this->command->info('   Email: admin@agrovida.com');
            $this->command->info('   Contraseña: admin123');
        } else {
            $admin = User::where('email', 'admin@agrovida.com')->first();
            if ($admin && !$admin->role_id) {
                $admin->update(['role_id' => $adminRole->id]);
                $this->command->info('✅ Usuario administrador actualizado con role_id!');
            } else {
                $this->command->warn('⚠️  El usuario administrador ya existe.');
            }
        }
    }
}
