<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentosController extends Controller
{
    public function TablaDepartamentos(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $query = Departamento::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
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
    public function AgregarDepartamentos(Request $request)
    {
        $this->validacion($request);
        $departamentos = new Departamento();
        $departamentos->nombre = $request->nombre;
        $departamentos->codigo_iso = $request->codigo_iso;
        $departamentos->save();
    }
    // La operación de Update CR[U]D
    public function ActualizarDepartamentos(Request $request)
    {
        $this->validacion($request);
        $departamentos = Departamento::find($request->id);
        $departamentos->nombre = $request->nombre;
        $departamentos->codigo_iso = $request->codigo_iso;
        $departamentos->save();
    }
    // La operación de Delete CR[U]D
    public function EliminarDepartamentos(Request $request)
    {
        $departamentos = Departamento::find($request->id);
        $departamentos->delete();
    }
    // La operación de validación
    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:30',
            'codigo_iso' => 'required|between:0,5',
        ]);
    }
}
