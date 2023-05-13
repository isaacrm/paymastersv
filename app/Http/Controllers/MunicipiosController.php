<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Http\Request;

class MunicipiosController extends Controller
{
    //
    public function TablaMunicipios(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        //$query = Municipio::with('departamento')->where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $query = Municipio::select('municipios.*', 'departamentos.nombre AS nombre_departamento')
                   ->join('departamentos', 'municipios.departamento_id', '=', 'departamentos.id')
                   ->where('municipios.nombre', 'like', '%' . $filtro . '%')
                   ->orderBy('municipios.id');
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
    public function AgregarMunicipios(Request $request)
    {
        //$this->validacion($request);
        $municipios = new Municipio();
        $municipios->nombre = $request->nombre;
        $municipios->departamento_id = $request->departamento_id;
        $municipios->save();
    }
}
