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
        $permisoVisualizar = Permission::where('name', 'dashboard')->first(); // Obtén el rol correspondiente
        $permisoInfoEmp = Permission::where('name', 'empleados.config')->first(); // Obtén el rol correspondiente
        $permisoDireccion = Permission::where('name', 'direccion')->first(); // Obtén el rol correspondiente
        $permisoRegEmp = Permission::where('name', 'registro.empresa')->first(); // Obtén el rol correspondiente
        $permisoRegMov = Permission::where('name', 'registro.movimientos')->first(); // Obtén el rol correspondiente
        $permisoRyP = Permission::where('name', 'roles.permisos')->first(); // Obtén el rol correspondiente
        $permisoTipoDoc = Permission::where('name', 'configuracion.doc')->first(); // Obtén el rol correspondiente
        $permisoUsuarios = Permission::where('name', 'roles.usuarios')->first(); // Obtén el rol correspondiente
        $permisoEmpleados = Permission::where('name', 'empleados.datos')->first(); // Obtén el rol correspondiente
        $permisoConf = Permission::where('name', 'configuracion.desc')->first(); // Obtén el rol correspondiente


        Role::create(['name' => 'SuperAdministrador'])->syncPermissions($permisoVisualizar,$permisoInfoEmp,$permisoDireccion,$permisoRegEmp,$permisoRegMov,$permisoRyP,$permisoTipoDoc,$permisoUsuarios, $permisoEmpleados, $permisoConf);
        Role::create(['name' => 'Administrador'])->syncPermissions($permisoVisualizar, $permisoInfoEmp, $permisoRegEmp, $permisoRegMov, $permisoUsuarios, $permisoEmpleados, $permisoTipoDoc, $permisoConf);
        Role::create(['name' => 'Visitante'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Planillero'])->syncPermissions($permisoVisualizar, $permisoEmpleados, $permisoRegEmp, $permisoRegMov, $permisoConf);
        Role::create(['name' => 'Empleado'])->syncPermissions($permisoVisualizar, $permisoEmpleados);
    }
}
