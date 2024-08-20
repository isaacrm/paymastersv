<?php

namespace Database\Seeders;

use App\Models\RentasMensuale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tramo = [
            [
                'tramo' => '1',
                'desde' => 0.01,
                'hasta' => 472.00,
                'porcentaje_aplicar' => 0.00,
                'sobre_exceso' => 0.00,
                'mas_fija' => 0.00,
            ],
            [
                'tramo' => '2',
                'desde' => 472.01,
                'hasta' => 895.24,
                'porcentaje_aplicar' => 0.10,
                'sobre_exceso' => 472.00,
                'mas_fija' => 17.67,
            ],
            [
                'tramo' => '3',
                'desde' => 895.25,
                'hasta' => 2038.10,
                'porcentaje_aplicar' => 0.20,
                'sobre_exceso' => 895.24,
                'mas_fija' => 60.00,
            ],
            [
                'tramo' => '4',
                'desde' => 2038.11,
                'hasta' => 9999.99,
                'porcentaje_aplicar' => 0.30,
                'sobre_exceso' => 2038.10,
                'mas_fija' => 288.57,
            ],
        ];

        // Crea registros de ejemplo en la tabla de usuarios
        foreach ($tramo as $item) {
            $renta = new RentasMensuale();
            $renta->tramo = $item['tramo'];
            $renta->desde = $item['desde'];
            $renta->hasta = $item['hasta'];
            $renta->porcentaje_aplicar = $item['porcentaje_aplicar'];
            $renta->sobre_exceso = $item['sobre_exceso'];
            $renta->mas_fija = $item['mas_fija'];
            $renta->save();
        }
    }
}
