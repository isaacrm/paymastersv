<?php

namespace Database\Seeders;

use App\Models\Estados_Civiles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadosCivilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Estados_Civiles::create(
            [
                "nombre" => "Soltero"
            ],
        );
        Estados_Civiles::create([
            "nombre" => "Casado"
        ],);
        Estados_Civiles::create([
            "nombre" => "Divorciado"
        ],);
    }
}
