<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Crear usuario solo si no existe (por email)
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // condición
            [
                'name' => 'Sak Noel',
                'password' => bcrypt('12345678')
            ]
        );

        // ✅ Crear rol solo si no existe
        $rol = Role::firstOrCreate([
            'name' => 'administrador'
        ]);

        // ✅ Asignar TODOS los permisos al rol
        $permisos = Permission::all();
        $rol->syncPermissions($permisos);

        // ✅ Asignar rol al usuario (sin duplicar)
        if (!$user->hasRole('administrador')) {
            $user->assignRole($rol);
        }
    }
}