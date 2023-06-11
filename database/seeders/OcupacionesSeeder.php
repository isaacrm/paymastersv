<?php

namespace Database\Seeders;

use App\Models\Ocupaciones;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OcupacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ocupaciones::create(["nombre" => "Tecnico informatico"],);
        Ocupaciones::create(["nombre" => "Ingeniero"],);
        Ocupaciones::create(["nombre" => "Abogado"],);
        Ocupaciones::create(["nombre" => "Diseñador gráfico"],);
        Ocupaciones::create(["nombre" => "Arquitecto"],);
        Ocupaciones::create(["nombre" => "Programador"],);
        Ocupaciones::create(["nombre" => "Contador"],);
        Ocupaciones::create(["nombre" => "Periodista"],);
        Ocupaciones::create(["nombre" => "Carpintero"],);
        Ocupaciones::create(["nombre" => "Electricista"],);
        Ocupaciones::create(["nombre" => "Modelo"],);
        Ocupaciones::create(["nombre" => "Economista"],);
        Ocupaciones::create(["nombre" => "Científico"],);
        Ocupaciones::create(["nombre" => "Conductor de autobús"],);
        Ocupaciones::create(["nombre" => "Consultor de negocios"],);
        Ocupaciones::create(["nombre" => "Electricista"],);
        Ocupaciones::create(["nombre" => "Mecánico"],);
        Ocupaciones::create(["nombre" => "Plomero"],);
        Ocupaciones::create(["nombre" => "Científico de datos"],);
        Ocupaciones::create(["nombre" => "Investigador"],);
        Ocupaciones::create(["nombre" => "Analista financiero"],);
        Ocupaciones::create(["nombre" => "Recepcionista"],);
        Ocupaciones::create(["nombre" => "Asistente administrativo"],);
        Ocupaciones::create(["nombre" => "Asesor financiero"],);
        Ocupaciones::create(["nombre" => "Técnico informático"],);
        Ocupaciones::create(["nombre" => "Escritor técnico"],);
        Ocupaciones::create(["nombre" => "Diseñador web"],);
        Ocupaciones::create(["nombre" => "Asistente de ventas"],);
        Ocupaciones::create(["nombre" => "Asistente de laboratorio"],);
        Ocupaciones::create(["nombre" => "Asistente de investigación"],);
        Ocupaciones::create(["nombre" => "Asesor de carrera"],);
        Ocupaciones::create(["nombre" => "Logístico"],);
        Ocupaciones::create(["nombre" => "Gerente de proyecto"],);
        Ocupaciones::create(["nombre" => "Empacador"],);
    }
}
