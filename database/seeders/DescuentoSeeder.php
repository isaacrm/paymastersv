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
                'nombre' => 'ISSS',
                'descripcion' => 'Instituto Salvadoreño del Seguro Social',
                'forma_aplicacion' => 'P',
                'obligatorio' => 'S',
                'valor_porcentaje' => 0.03,
            ],
            [
                'nombre' => 'AFP',
                'descripcion' => 'Administradora de Fondo de Pesiones',
                'forma_aplicacion' => 'P',
                'obligatorio' => 'S',
                'valor_porcentaje' => 0.0725,
            ],
            [
                'nombre' => 'ISR',
                'descripcion' => 'Impuesto Sobre la Renta',
                'forma_aplicacion' => 'T',
                'obligatorio' => 'S',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Sindicato',
                'descripcion' => 'Cuando se realizan descuentos para el sindicato de la empresa',
                'forma_aplicacion' => 'M',
                'obligatorio' => 'N',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Cuota de Préstamo',
                'descripcion' => 'La cuota que se le descuenta al empleado por algún préstamo realizado',
                'forma_aplicacion' => 'M',
                'obligatorio' => 'N',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Procuraduría',
                'descripcion' => 'Cuando es necesario aportarle a la Procuraduría',
                'forma_aplicacion' => 'M',
                'obligatorio' => 'N',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Cuota Alimenticia',
                'descripcion' => 'Cuando el empleado debe aportar cuota alimenticia',
                'forma_aplicacion' => 'M',
                'obligatorio' => 'N',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Días de Ausencia',
                'descripcion' => 'Descuento del día cuando el empleado no se presenta sin justificación',
                'forma_aplicacion' => 'F',
                'obligatorio' => 'S',
                'valor_porcentaje' => null,
            ],
            [
                'nombre' => 'Horas de Ausencia',
                'descripcion' => 'Descuento de horas cuando el empleado no se presenta sin justificación',
                'forma_aplicacion' => 'F',
                'obligatorio' => 'S',
                'valor_porcentaje' => null,
            ],
        ];

        // Crea registros de ejemplo en la tabla de usuarios
        foreach ($descuentos as $item) {
            $descuento = new Descuento();
            $descuento->nombre = $item['nombre'];
            $descuento->descripcion = $item['descripcion'];
            $descuento->forma_aplicacion = $item['forma_aplicacion'];
            $descuento->obligatorio = $item['obligatorio'];
            $descuento->valor_porcentaje = $item['valor_porcentaje'];
            $descuento->save();
        }
    }
}
