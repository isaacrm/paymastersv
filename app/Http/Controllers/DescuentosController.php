<?php

namespace App\Http\Controllers;
use App\Models\Descuento;
use Illuminate\Http\Request;

class DescuentosController extends Controller
{
    public function TablaDescuentos(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $query = Descuento::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
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
    public function AgregarDescuentos(Request $request)
    {
        $this->validacion($request);
        $descuentos = new Descuento();
        $descuentos->nombre = $request->nombre;
        $descuentos->descripcion = $request->descripcion;
        $descuentos->forma_aplicacion = $request->forma_aplicacion;
        $descuentos->descuento = $request->descuento;
        $descuentos->valor_porcentaje = $request->valor_porcentaje;
        $descuentos->save();
    }
    // La operación de Update CR[U]D
    public function ActualizarDescuentos(Request $request)
    {
        $this->validacion($request);
        $descuentos = Descuento::find($request->id);
        $descuentos->nombre = $request->nombre;
        $descuentos->descripcion = $request->descripcion;
        $descuentos->forma_aplicacion = $request->forma_aplicacion;
        $descuentos->descuento = $request->descuento;
        $descuentos->valor_porcentaje = $request->valor_porcentaje;
        $descuentos->save();
    }
    // La operación de Delete CR[U]D
    public function EliminarDescuentos(Request $request)
    {
        $descuentos = Descuento::find($request->id);
        $descuentos->delete();
    }
    // La operación de validación
    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:30',
            'descripcion' => 'required|max:100',
            'forma_aplicacion' => 'required|max:1',
            'descuento' => 'required',
            'valor_porcentaje' => 'between:0,9999',
        ]);
    }
}