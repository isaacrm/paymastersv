<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'registros.id',
            'registros.dias_trabajados',
            'registros.horas_trabajadas',
            'registros.horas_adicionales',
            'registros.horas_ausencia',
            'registros.empleados_id',
            'registros.planillas_id',
            'empleados.salario_base',
            'empleados.identificacion',
            DB::raw("empleados.primer_nombre || ' ' || empleados.segundo_nombre || ' ' || empleados.apellido_paterno || ' ' || empleados.apellido_materno AS nombre_completo"),
            DB::raw('NVL(SUM(registro_descuentos.monto), 0) AS suma_descuentos'),
            DB::raw('NVL(SUM(registro_ingresos.monto), 0) AS suma_ingresos')
        )
            ->join('empleados', 'registros.empleados_id', '=', 'empleados.id')
            ->Leftjoin('registro_ingresos', 'registro_ingresos.registros_id', '=', 'registros.id')
            ->Leftjoin('registro_descuentos', 'registro_descuentos.registros_id', '=', 'registros.id')
            ->where('registros.id', 'like', '%' . $filtro . '%')
            ->where('registros.planillas_id', $planillas_id)
            ->groupBy('registros.id', 'registros.dias_trabajados', 'registros.horas_trabajadas', 'registros.horas_adicionales', 'registros.horas_ausencia', 'registros.empleados_id', 'registros.planillas_id', 'empleados.salario_base', 'empleados.identificacion', 'empleados.primer_nombre', 'empleados.segundo_nombre', 'empleados.apellido_paterno', 'empleados.apellido_materno')
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
}
