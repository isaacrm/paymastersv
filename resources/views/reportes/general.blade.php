<!DOCTYPE html>
<html lang="es">
<!--Inspirado en https://github.com/josegus/dompdf-manipulation-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Planilla General</title>
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
                    <div class="bold">PLANILLA GENERAL DE LOS EMPLEADOS</div>
                    <div>DEPARTAMENTO DE RECURSOS HUMANOS</div>
                </td>
            </tr>
        </table>

        <table class="mt" cellpadding="0" cellspacing="0">
            <tr>
                <td class="col">
                    <div>PLANILLA GENERAL</div>
                    <div><span class="bold">Nota</span>: Esta tabla es solamente una referencia del caso más ideal.
                    </div>
                </td>
            </tr>
        </table>

        <table class="mt">
            <tr>
                <td class="col">
                    <div><span class="bold">ID:</span> {{ $planillas_id }} </div>
                    <div><span class="bold">Fecha Generación de Planilla:</span> {{ $generacion_planilla }} </div>
                    <div><span class="bold">Fecha Generación de Reporte:</span> {{ $generacion_reporte }} </div>
                </td>

                <td class="col">
                    <div><span class="bold">Período:</span> {{ $periodo }} </div>
                    <div><span class="bold">Días Laborales:</span> {{ $dias_laborales }} </div>
                    <div><span class="bold">Horas Laborales:</span> {{ $horas_laborales }} </div>
                </td>
            </tr>
        </table>

        <table class="items-table mt" cellpadding="0" cellspacing="0">
            <thead>
                <tr class="heading">
                    <th class="col">Nombre Completo</th>
                    <th class="col">Identificación</th>
                    <th class="col">Días Trabajados</th>
                    <th class="col">Días Ausente</th>
                    <th class="col">Días Permiso</th>
                    <th class="col">Horas Trabajadas</th>
                    <th class="col">Horas Adicionales</th>
                    <th class="col">Horas Ausencia</th>
                    <th class="col">Sueldo</th>
                    <th class="col">Suma de Ingresos</th>
                    <th class="col">Suma de Descuentos</th>
                    <th class="col">Líquido a recibir</th>
                </tr>
            </thead>
            @foreach ($tabla as $columna)
                <tr class="item">
                    <td>{{ $columna['nombre_completo'] }}</td>
                    <td>{{ $columna['identificacion'] }}</td>
                    <td>{{ $columna['dias_trabajados'] }}</td>
                    <td>{{ $columna['dias_ausente'] }}</td>
                    <td>{{ $columna['dias_permiso'] }}</td>
                    <td>{{ $columna['horas_trabajadas'] }}</td>
                    <td>{{ $columna['horas_adicionales'] }}</td>
                    <td>{{ $columna['horas_ausencia'] }}</td>
                    <td>{{ number_format($columna['salario_base'], 2) }}</td>
                    <td>{{ number_format($columna['total_ingresos'], 2) }}</td>
                    <td>{{ number_format($columna['total_descuentos'], 2) }}</td>
                    <td>{{ number_format($columna['salario_liquido'], 2) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</body>

</html>
