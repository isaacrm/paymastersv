<?php

namespace Database\Seeders;

use App\Models\Puesto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuestosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Puesto::create(["nombre"=>"Director ejecutivo", "nro_plazas"=>1, "salario_desde"=>2500, "salario_hasta"=>5000]);
        Puesto::create(["nombre"=>"Gerente administrativo", "nro_plazas"=>1, "salario_desde"=>1500, "salario_hasta"=>3500, "superior_id"=>1]);
        Puesto::create(["nombre"=>"Jefe de informatica", "nro_plazas"=>1, "salario_desde"=>1000, "salario_hasta"=>2500, "superior_id"=>2]);
        Puesto::create(["nombre"=>"Tecnico informatico", "nro_plazas"=>12, "salario_desde"=>500, "salario_hasta"=>1500, "superior_id"=>3]);
    }
}
