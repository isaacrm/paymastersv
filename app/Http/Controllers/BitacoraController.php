<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
{
    public function TablaBitacora(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;

        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Activity::select('ACTIVITY_LOG.*', 'users.name as causer_name', 'empleados.primer_nombre as empleado_name',
                    DB::raw(" 'El día ' || TO_CHAR(ACTIVITY_LOG.updated_at, 'DD') || ' del ' || 
                    TO_CHAR(ACTIVITY_LOG.updated_at, 'MM') || ' del ' || TO_CHAR(ACTIVITY_LOG.updated_at, 'YYYY') || 
                    ' a las ' || TO_CHAR(ACTIVITY_LOG.updated_at, 'HH24:MI:SS') as formatted_updated_at"))
                ->join('users', 'ACTIVITY_LOG.causer_id', '=', 'users.id')
                ->join('empleados', 'ACTIVITY_LOG.subject_id', '=', 'empleados.id')
                ->where(function ($query) use ($filtro) {
                    $query->where('ACTIVITY_LOG.description', 'like', '%' . $filtro . '%')
                    ->orWhere('ACTIVITY_LOG.properties', 'like', '%' . $filtro . '%')
                    ->orWhere('ACTIVITY_LOG.updated_at', 'like', '%' . $filtro . '%')                                        
                    ->orWhere('empleados.primer_nombre', 'like', '%' . $filtro . '%')                    
                    ->orWhere('users.name', 'like', '%' . $filtro . '%');
                   })
            ->orderBy('ACTIVITY_LOG.id');
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
