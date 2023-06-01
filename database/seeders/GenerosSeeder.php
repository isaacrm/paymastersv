<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Generos;

class GenerosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Generos::create(
            [
                'nombre' => 'Hombre',
            ],
        );
        Generos::create(
            [
                'nombre' => 'Mujer',
            ]
        );
    }
}
