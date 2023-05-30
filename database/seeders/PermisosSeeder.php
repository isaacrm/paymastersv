<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'Inicio']);
        Permission::create(['name' => 'Información de empleados']);
        Permission::create(['name' => 'Direccion']);
        Permission::create(['name' => 'Registro Empresa']);
        Permission::create(['name' => 'Registro Movimientos']);
        Permission::create(['name' => 'Roles y Permisos']);
        Permission::create(['name' => 'TipoDoc']);
        Permission::create(['name' => 'Usuarios']);
        Permission::create(['name' => 'Empleados']);
        Permission::create(['name' => 'Configuración']);



    }
}
