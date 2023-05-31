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
        Permission::create(['name' => 'dashboard']);
        Permission::create(['name' => 'empleados.config']);
        Permission::create(['name' => 'direccion']);
        Permission::create(['name' => 'registro.empresa']);
        Permission::create(['name' => 'registro.movimientos']);
        Permission::create(['name' => 'roles.permisos']);
        Permission::create(['name' => 'configuracion.doc']);
        Permission::create(['name' => 'roles.usuarios']);
        Permission::create(['name' => 'empleados.datos']);
        Permission::create(['name' => 'configuracion.desc']);



    }
}
