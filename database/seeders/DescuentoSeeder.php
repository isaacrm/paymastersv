<?php

namespace Database\Seeders;

use App\Models\Descuento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DescuentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $descuentos = [
            [
                'nombre' => 'ISR',
                'descripcion' => 'Impuesto sobre la Renta',
                'forma_aplicacion' => 'T',
                'obligatorio' => 'S',
                'tabla_aplicar' => 'rentas_mensuales',
            ],
            // Puedes agregar más usuarios aquí
        ];

        // Crea registros de ejemplo en la tabla de usuarios
        foreach ($descuentos as $item) {
            $descuento = new Descuento();
            $descuento->nombre = $item['nombre'];
            $descuento->descripcion = $item['descripcion'];
            $descuento->forma_aplicacion = $item['forma_aplicacion'];
            $descuento->obligatorio = $item['obligatorio'];
            $descuento->tabla_aplicar = $item['tabla_aplicar'];
            $descuento->save();
        }
    }
}
