<?php

namespace App\Http\Controllers;

use App\Models\Movimientos;
use Illuminate\Http\Request;

class MovimientosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $centro_costos_id = $request->centro_costos_id;

        $query = Movimientos::where('descripcion', 'like', '%' . $filtro . '%')
            ->where('centro_costos_id', '=', $centro_costos_id)
            ->orderBy('id');

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
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $datos = new Movimientos();
        $datos->descripcion = $request->descripcion;
        $datos->monto = $request->monto;
        $datos->centro_costos_id = $request->centro_costos_id;
        $datos->planillas_id = $request->planillas_id;
        $datos->operacion = '+';
        $datos->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(movimientos $movimientos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(movimientos $movimientos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validacion($request);
        $datos = Movimientos::find($request->id);
        $datos->descripcion = $request->descripcion;
        $datos->monto = $request->monto;
        $datos->centro_costos_id = $request->centro_costos_id;
        
        if($request->planillas_id){
            $datos->planillas_id = $request->planillas_id;
        }

        $datos->operacion = '+';
        $datos->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $datos = Movimientos::find($request->id);
        $datos->delete();
    }

    private function validacion(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|max:150',
            'monto' => 'required|min:0',
            'centro_costos_id' => 'required'
        ]);
    }
}
