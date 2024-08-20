<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuarios Administradores
        $superadmin = User::create([
            'name' => 'sadmin@admin.com',
            'email' => 'sadmin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ]);

        $admin = User::create([
            'name' => 'admin@admin.com',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ]);

        $planillero = User::create([
            'name' => 'planillero@planillero.com',
            'email' => 'planillero@planillero.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ]);

        $empleado = User::create([
            'name' => 'empleado@empleado.com',
            'email' => 'empleado@empleado.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ]);

        $visitante = User::create([
            'name' => 'visitante@visitante.com',
            'email' => 'visitante@visitante.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ]);

        $role1 = Role::findByName('Planillero');
        $role2 = Role::findByName('SuperAdministrador');
        $role3 = Role::findByName('Administrador');
        $role4 = Role::findByName('Visitante');
        $role5 = Role::findByName('Empleado');

        $permiso1 = Permission::findByName('dashboard');
        $permiso2 = Permission::findByName('direccion');
        $permiso3 = Permission::findByName('empleados.config');
        $permiso4 = Permission::findByName('registro.empresa');
        $permiso5 = Permission::findByName('registro.movimientos');
        $permiso6 = Permission::findByName('roles.permisos');
        $permiso7 = Permission::findByName('configuracion.doc');
        $permiso8 = Permission::findByName('roles.usuarios');
        $permiso8 = Permission::findByName('empleados.datos');
        $permiso9 = Permission::findByName('configuracion.desc');



        $superadmin->assignRole($role2);
        //$superadmin->givePermissionTo($permiso);

        $admin->assignRole($role3);
        //$admin->givePermissionTo($permiso);

        $visitante->assignRole($role4);
        //$visitante->givePermissionTo($permiso);

        $empleado->assignRole($role5);
        //$empleado->givePermissionTo($permiso);

        $planillero->assignRole($role1);
        //$planillero->givePermissionTo($permiso);
    }
}
