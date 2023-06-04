<!DOCTYPE html>
<html lang="es">
<!--Inspirado en https://github.com/josegus/dompdf-manipulation-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Detalle de salario</title>
    <link href="{{ public_path('assets/css/tabla.css') }}" rel="stylesheet" type="text/css">
    <style>
        * {
            font-family: 'Lato', sans-serif;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="bold">PAYMASTER SV</div>
                    <div><span class="bold">NIT</span>: 0123-456789-012-3 | <span class="bold">NRC</span>: 12345-6
                    </div>
                    <div>Sistema de Planillas.</div>
                    <div>P. Sherman, Calle Wallaby 42, San Salvador </div>
                    <div>El Salvador</div>
                </td>
                <td class="w-50 text-right">
                    <img src="{{ public_path('imagenes/paymaster.svg') }}" width="50%">
                </td>
            </tr>
        </table>

        <table class="mt" cellpadding="0" cellspacing="0">
            <tr>
                <td class="col">
                    <div class="bold">PAGO AL EMPLEADO</div>
                    <div>DEPARTAMENTO DE RECURSOS HUMANOS</div>
                    <div><span class="bold">Nota</span>: A continuación se detalla su salario del período
                        {{ $periodo }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="mt">
            <tr>
                <td class="col">
                    <div><span class="bold">Nombre Completo:</span> {{ $nombre_completo }}</div>
                    <div><span class="bold">Idenficación:</span> {{ $identificacion }} </div>
                </td>

                <td class="col">
                    <div><span class="bold">Salario Base:</span> ${{ $salario_base }} </div>
                    <div><span class="bold">Total de Ingresos:</span> ${{ $total_ingresos }} </div>
                </td>

                <td class="col">
                    <div><span class="bold">Salario Total:</span> ${{ $salario_total }} </div>
                    <div><span class="bold">Total de descuentos:</span> ${{ $total_descuentos }} </div>
                </td>

                <td class="col">
                    <div><span class="bold">Líquido a recibir:</span> ${{ $salario_liquido }} </div>
                    <div><span class="bold">Fecha Generación de Reporte:</span> {{ $generacion_reporte }} </div>
                </td>
            </tr>
        </table>

        <table class="mt" cellpadding="0" cellspacing="0">
            <tr>
                <td class="col">
                    <div><span class="bold">DETALLE DE INGRESOS</span></div>
                </td>
            </tr>
        </table>

        <table class="items-table mt" cellpadding="0" cellspacing="0">
            <thead>
                <tr class="heading">
                    @foreach ($ingresos as $columna)
                        <th class="col">
                            {{ $columna['nombre'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tr class="item">
                @foreach ($ingresos as $columna)
                    <td>{{ number_format($columna['monto'], 2) }}</td>
                @endforeach
            </tr>
        </table>

        <table class="mt" cellpadding="0" cellspacing="0">
            <tr>
                <td class="col">
                    <div><span class="bold">DETALLE DE DESCUENTOS</span></div>
                </td>
            </tr>
        </table>

        <table class="items-table mt" cellpadding="0" cellspacing="0">
            <thead>
                <tr class="heading">
                    @foreach ($descuentos as $columna)
                        <th class="col">
                            {{ $columna['nombre'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tr class="item">
                @foreach ($descuentos as $columna)
                    <td>{{ number_format($columna['monto'], 2) }}</td>
                @endforeach
            </tr>
        </table>
    </div>
</body>

</html>
