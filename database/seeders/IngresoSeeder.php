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
                'nombre' => 'Horas Extra',
                'descripcion' => 'Son las horas extra que se le pagan al empleado',
                'forma_aplicacion' => 'F',
                'obligatorio' => 'S',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Aguinaldo',
                'descripcion' => 'Lo que se paga a final de año',
                'forma_aplicacion' => 'T',
                'obligatorio' => 'N',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Comision por ventas',
                'descripcion' => 'Lo que se da por ventas',
                'forma_aplicacion' => 'P',
                'obligatorio' => 'N',
                'valor_porcentaje' => 0.20,
            ],
            [
                'nombre' => 'Bonos',
                'descripcion' => 'Bonificaciones que se le dan al empleado',
                'forma_aplicacion' => 'M',
                'obligatorio' => 'N',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Vacaciones',
                'descripcion' => 'Vacaciones remuneradas',
                'forma_aplicacion' => 'P',
                'obligatorio' => 'N',
                'valor_porcentaje' => 0.30,
            ],
            [
                'nombre' => 'Vacacion proporcional',
                'descripcion' => 'Vacaciones remuneradas a nivel proporcional',
                'forma_aplicacion' => 'F',
                'obligatorio' => 'N',
                'valor_porcentaje' => null,
            ],
        ];

        // Crea registros de ejemplo en la tabla de usuarios
        foreach ($ingresos as $item) {
            $ingreso = new Ingreso();
            $ingreso->nombre = $item['nombre'];
            $ingreso->descripcion = $item['descripcion'];
            $ingreso->forma_aplicacion = $item['forma_aplicacion'];
            $ingreso->obligatorio = $item['obligatorio'];
            $ingreso->valor_porcentaje = $item['valor_porcentaje'];
            $ingreso->save();
        }
    }
}
