<?php

namespace App\Http\Controllers;

use App\Models\Ocupaciones;
use Illuminate\Http\Request;

class OcupacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;

        $query = Ocupaciones::where(function ($query) use ($filtro) {
            $query->where('nombre', 'like', '%' . $filtro . '%');
        })
            ->orderBy('id');
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validacion($request);
        $objeto = new Ocupaciones();
        $objeto->nombre = $request->nombre;
        $objeto->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Ocupaciones $ocupaciones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ocupaciones $ocupaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ocupaciones $ocupaciones)
    {
        $this->validacion($request);
        $objeto = Ocupaciones::find($request->id);
        $objeto->nombre = $request->nombre;
        $objeto->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $objeto = Ocupaciones::find($request->id);
        $objeto->delete();
    }

    public function consultar_id_nombre()
    {
        $datos = Ocupaciones::select('id', 'nombre as name')->get();
        return response()->json($datos, 200);
    }

    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:150',
        ]);
    }
}
