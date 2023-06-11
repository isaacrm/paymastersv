<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IngresosController extends Controller
{
    public function TablaIngresos(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $query = Ingreso::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $tuplas = $query->count();
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();
        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro
        ];
        return response()->json([
            'detalle' => $detalle,
            'paginacion' => $paginacion,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    // La operación de Create [C]RUD
    public function AgregarIngresos(Request $request)
    {
        $this->validacion($request);
        $ingresos = new Ingreso();
        $ingresos->nombre = $request->nombre;
        $ingresos->descripcion = $request->descripcion;
        $ingresos->forma_aplicacion = $request->forma_aplicacion;
        $ingresos->obligatorio = $request->obligatorio;
        $ingresos->valor_porcentaje = $request->valor_porcentaje;
        $ingresos->save();

        //echo '<pre>';
        //var_dump($ingresos); // Imprime el contenido del objeto $ingresos
        //echo '</pre>';
        exit();
    }
    // La operación de Update CR[U]D
    public function ActualizarIngresos(Request $request)
    {
        $this->validacion($request);
        $ingresos = Ingreso::find($request->id);
        $ingresos->nombre = $request->nombre;
        $ingresos->descripcion = $request->descripcion;
        $ingresos->forma_aplicacion = $request->forma_aplicacion;
        $ingresos->obligatorio = $request->obligatorio;
        $ingresos->valor_porcentaje = $request->valor_porcentaje;
        $ingresos->save();
    }
    // La operación de Delete CR[U]D
    public function EliminarIngresos(Request $request)
    {
        $ingresos = Ingreso::find($request->id);
        $ingresos->delete();
    }
    // La operación de validación
    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:30',
            'descripcion' => 'required|max:100',
            'forma_aplicacion' => 'required|max:1',
            'obligatorio' => 'required',
            'valor_porcentaje' => 'between:0,100',
        ]);
    }
}
