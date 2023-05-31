<?php

namespace Database\Seeders;

use App\Models\Ingreso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngresoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingresos = [
            [
                'nombre' => 'Aguinaldo',
                'descripcion' => 'Aguinaldo fin de año',
                'forma_aplicacion' => 'T',
                'obligatorio' => 'S',
                'tabla_aplicar' => 'aguinaldos',
            ],
            // Puedes agregar más usuarios aquí
        ];

        // Crea registros de ejemplo en la tabla de usuarios
        foreach ($ingresos as $item) {
            $ingreso = new Ingreso();
            $ingreso->nombre = $item['nombre'];
            $ingreso->descripcion = $item['descripcion'];
            $ingreso->forma_aplicacion = $item['forma_aplicacion'];
            $ingreso->obligatorio = $item['obligatorio'];
            $ingreso->tabla_aplicar = $item['tabla_aplicar'];
            $ingreso->save();
        }
    }
}
