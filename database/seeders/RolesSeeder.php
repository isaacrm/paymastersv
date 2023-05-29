<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisoVisualizar = Permission::where('name', 'Inicio')->first(); // Obtén el rol correspondiente

        Role::create(['name' => 'Administrador'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'SuperAdministrador'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Contador'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Asistente'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Visitante'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Usuario'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Planillero'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Empleado'])->syncPermissions($permisoVisualizar);


    }
}
