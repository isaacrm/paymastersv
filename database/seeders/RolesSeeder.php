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
        $permisoInfoEmp = Permission::where('name', 'Información de empleados')->first(); // Obtén el rol correspondiente
        $permisoDireccion = Permission::where('name', 'Direccion')->first(); // Obtén el rol correspondiente
        $permisoRegEmp = Permission::where('name', 'Registro Empresa')->first(); // Obtén el rol correspondiente
        $permisoRegMov = Permission::where('name', 'Registro Movimientos')->first(); // Obtén el rol correspondiente
        $permisoRyP = Permission::where('name', 'Roles y Permisos')->first(); // Obtén el rol correspondiente
        $permisoTipoDoc = Permission::where('name', 'TipoDoc')->first(); // Obtén el rol correspondiente
        $permisoUsuarios = Permission::where('name', 'Usuarios')->first(); // Obtén el rol correspondiente
        $permisoEmpleados = Permission::where('name', 'Empleados')->first(); // Obtén el rol correspondiente
        $permisoConf = Permission::where('name', 'Configuración')->first(); // Obtén el rol correspondiente


        Role::create(['name' => 'SuperAdministrador'])->syncPermissions($permisoVisualizar,$permisoInfoEmp,$permisoDireccion,$permisoRegEmp,$permisoRegMov,$permisoRyP,$permisoTipoDoc,$permisoUsuarios, $permisoEmpleados, $permisoConf);
        Role::create(['name' => 'Administrador'])->syncPermissions($permisoVisualizar, $permisoInfoEmp, $permisoRegEmp, $permisoRegMov, $permisoUsuarios, $permisoEmpleados, $permisoTipoDoc, $permisoConf);
        Role::create(['name' => 'Visitante'])->syncPermissions($permisoVisualizar);
        Role::create(['name' => 'Planillero'])->syncPermissions($permisoVisualizar, $permisoEmpleados, $permisoRegEmp, $permisoRegMov, $permisoConf);
        Role::create(['name' => 'Empleado'])->syncPermissions($permisoVisualizar, $permisoEmpleados);
    }
}
