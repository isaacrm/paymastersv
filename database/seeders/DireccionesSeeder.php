<?php

namespace Database\Seeders;

use App\Models\Direccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DireccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Direccion::create(["calle" => "Los sisimiles", "colonia" => "Monserrat", "identificador_casa" => "14b", "apto_local" => 2, "municipio_id" => 193]);
    }
}
