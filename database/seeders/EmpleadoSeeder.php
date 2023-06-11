<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empleados;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generar datos de prueba para los campos
        $empleado = new Empleados();
        $empleado->apellido_paterno = 'Pérez';
        $empleado->apellido_materno = 'González';
        $empleado->apellido_casada = 'Gómez';
        $empleado->primer_nombre = 'María';
        $empleado->segundo_nombre = 'Elena';
        $empleado->fecha_nacimiento = '1990-05-15';
        $empleado->identificacion = '12345678-9';
        $empleado->nit = '12345678-9';
        $empleado->isss = '123456789';
        $empleado->nup = '987654321';
        $empleado->email_personal = 'maria@example.com';
        $empleado->fecha_ingreso = '2020-01-01';
        $empleado->email_profesional = 'maria.perez@example.com';
        $empleado->salario_base = 1000.00;
        $empleado->estados_civiles_id = 2;
        $empleado->generos_id = 2;
        $empleado->ocupaciones_id = 1;
        $empleado->tipo_documentos_id = 1;
        $empleado->direcciones_id = 1;
        $empleado->puestos_id = 1;
        $empleado->save();
    }
}
