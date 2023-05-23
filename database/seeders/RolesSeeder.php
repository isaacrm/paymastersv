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
        Role::create(['name' => 'Administrador'])->givePermissionTo('Inicio');
        Role::create(['name' => 'SuperAdministrador'])->givePermissionTo('Inicio');
        Role::create(['name' => 'Contador'])->givePermissionTo('Inicio');
        Role::create(['name' => 'Asistente'])->givePermissionTo('Inicio');
        Role::create(['name' => 'Visitante'])->givePermissionTo('Inicio');
        Role::create(['name' => 'Usuario'])->givePermissionTo('Inicio');
        Role::create(['name' => 'Planillero'])->givePermissionTo('Inicio');
        Role::create(['name' => 'Empleado'])->givePermissionTo('Inicio');


    }
}
