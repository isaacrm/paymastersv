<?php

namespace App\Http\Controllers;

use App\Models\DetalleRegistro;
use App\Models\Planillas;
use App\Models\Registro;
use App\Models\RegistroDescuento;
use App\Models\RegistroIngreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;

class RegistroController extends Controller
{
    public function index(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $planillas_id = $request->planillas_id;

        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Registro::select(
            'registros.*',
            'empleados.identificacion',
            'detalle_registros.salario_base',
            'detalle_registros.total_ingresos',
            'detalle_registros.salario_total',
            'detalle_registros.total_descuentos',
            'detalle_registros.salario_liquido',
            DB::raw("empleados.primer_nombre || ' ' || empleados.segundo_nombre || ' ' || empleados.apellido_paterno || ' ' || empleados.apellido_materno AS nombre_completo"),
        )
            ->join('empleados', 'registros.empleados_id', '=', 'empleados.id')
            ->Leftjoin('registro_ingresos', 'registro_ingresos.registros_id', '=', 'registros.id')
            ->Leftjoin('registro_descuentos', 'registro_descuentos.registros_id', '=', 'registros.id')
            ->Leftjoin('detalle_registros', 'detalle_registros.registros_id', '=', 'registros.id')
            ->where('empleados.identificacion', 'like', '%' . $filtro . '%')
            ->where('registros.planillas_id', $planillas_id)
            ->distinct()
            ->orderBy('registros.id');
        // $query = CentroDeCostos::all()->orderBy('id');
        $tuplas = $query->count();

        // Obtener los datos de la página actual
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();

        // Informacion pertinente a la paginacion para llamarlos en la vista
        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro
        ];

        // El json que se manda a la vista para poder visualizar la información
        return response()->json([
            'detalle' => $detalle,
            'paginacion' => $paginacion,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function pdf(Request $request)
    {
        $encabezado = Planillas::where('id', $request->planillas_id)->first();
        $detalle = Registro::select(
            'registros.*',
            'empleados.identificacion',
            'detalle_registros.salario_base',
            'detalle_registros.total_ingresos',
            'detalle_registros.salario_total',
            'detalle_registros.total_descuentos',
            'detalle_registros.salario_liquido',
            DB::raw("empleados.primer_nombre || ' ' || empleados.segundo_nombre || ' ' || empleados.apellido_paterno || ' ' || empleados.apellido_materno AS nombre_completo"),
        )
            ->join('empleados', 'registros.empleados_id', '=', 'empleados.id')
            ->Leftjoin('registro_ingresos', 'registro_ingresos.registros_id', '=', 'registros.id')
            ->Leftjoin('registro_descuentos', 'registro_descuentos.registros_id', '=', 'registros.id')
            ->Leftjoin('detalle_registros', 'detalle_registros.registros_id', '=', 'registros.id')
            ->where('registros.planillas_id', $request->planillas_id)
            ->orderBy('registros.id')
            ->distinct()
            ->get();

        $data = [
            'periodo' => $encabezado->mes_periodo . '/' . $encabezado->anyo_periodo,
            'generacion_planilla' => Carbon::parse($encabezado->fecha_generacion)->format('d/m/Y'),
            'generacion_reporte' => Carbon::now()->format('d/m/Y'),
            'dias_laborales' => $encabezado->dias_laborales,
            'horas_laborales' => $encabezado->horas_laborales,
            'planillas_id' => $request->planillas_id,
            // Todo el array de la consulta
            'tabla' => $detalle,
        ];
        // Generando PDF. Carga el blade que se encuentra en /views/reportes/general, el cual es la version html y css del pdf.
        $pdf = SnappyPdf::loadView('reportes.general', $data)->setOrientation('landscape')->setPaper('letter');
        return $pdf->output('planilla_general.pdf');
    }

    public function pdfIndividual(Request $request)
    {
        $encabezado = DetalleRegistro::select(
            'detalle_registros.*',
            'registros.empleados_id',
            'registros.planillas_id',
            'empleados.primer_nombre',
            'empleados.segundo_nombre',
            'empleados.apellido_paterno',
            'empleados.apellido_materno',
            'empleados.identificacion',
            'planillas.mes_periodo',
            'planillas.anyo_periodo'
        )
            ->join('registros', 'detalle_registros.registros_id', '=', 'registros.id')
            ->join('empleados', 'registros.empleados_id', '=', 'empleados.id')
            ->join('planillas', 'registros.planillas_id', '=', 'planillas.id')
            ->where('registros.id', $request->registro_id)
            ->where('registros.planillas_id', $request->planillas_id)
            ->orderBy('registros.id')
            ->first();

        $ingresos = RegistroIngreso::select(
            'registro_ingresos.*',
            'ingresos.nombre'
        )
            ->join('ingresos', 'registro_ingresos.ingresos_id', '=', 'ingresos.id')
            ->where('registro_ingresos.registros_id', $request->registro_id)
            ->get();

        $descuentos = RegistroDescuento::select(
            'registro_descuentos.*',
            'descuentos.nombre'
        )
            ->join('descuentos', 'registro_descuentos.descuentos_id', '=', 'descuentos.id')
            ->where('registro_descuentos.registros_id', $request->registro_id)
            ->get();

        $data = [
            'periodo' => $encabezado->mes_periodo . '/' . $encabezado->anyo_periodo,
            'nombre_completo' => $encabezado->primer_nombre . ' ' . $encabezado->segundo_nombre . ' ' . $encabezado->apellido_paterno . ' ' . $encabezado->apellido_materno,
            'identificacion' => $encabezado->identificacion,
            'salario_base' => number_format($encabezado->salario_base, 2),
            'total_ingresos' => number_format($encabezado->total_ingresos, 2),
            'salario_total' => number_format($encabezado->salario_total, 2),
            'total_descuentos' => number_format($encabezado->total_descuentos, 2),
            'salario_liquido' => number_format($encabezado->salario_liquido, 2),
            'generacion_reporte' => Carbon::now()->format('d/m/Y'),
            'ingresos' => $ingresos,
            'descuentos' => $descuentos,
        ];
        // Generando PDF. Carga el blade que se encuentra en /views/reportes/general, el cual es la version html y css del pdf.
        $pdf = SnappyPdf::loadView('reportes.personal', $data)->setOrientation('landscape')->setPaper('letter');
        return $pdf->output('pago_personal.pdf');
    }
}
