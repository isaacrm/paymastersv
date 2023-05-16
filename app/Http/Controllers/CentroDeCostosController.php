<?php

namespace App\Http\Controllers;

use App\Models\CentroDeCostos;
use Illuminate\Http\Request;

class CentroDeCostosController extends Controller
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
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = CentroDeCostos::where('id', 'like', '%' . $filtro . '%')->orderBy('id');
        // $query = CentroDeCostos::all()->orderBy('id');
        $tuplas = $query->count();
        
        // Obtener los datos de la página actual
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
        ->take($filasPorPagina)
        ->get();
        
        //Mandamos los meses y no el id
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        foreach ($detalle as $element) {
            $element['mes_del'] = $meses[$element['mes_del']];
            $element['mes_al'] = $meses[$element['mes_al']];
        }

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
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $datos = new CentroDeCostos();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $datos->mes_del = array_search($request->mes_del, $meses);
        $datos->mes_al = array_search($request->mes_al, $meses);
        $datos->anyo = $request->anyo;
        $datos->presupuesto_inicial = $request->presupuesto_inicial;
        $datos->presupuesto_restante = $request->presupuesto_restante;
        $datos->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(CentroDeCostos $centroDeCostos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CentroDeCostos $centroDeCostos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $this->validacion($request);
        $datos = CentroDeCostos::find($request->id);
        $datos->mes_del = array_search($request->mes_del, $meses);
        $datos->mes_al = array_search($request->mes_al, $meses);
        $datos->anyo = $request->anyo;
        $datos->presupuesto_inicial = $request->presupuesto_inicial;
        $datos->presupuesto_restante = $request->presupuesto_restante;
        $datos->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $datos = CentroDeCostos::find($request->id);
        $datos->delete();
    }

    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'mes_del' => 'required',
            'mes_al' => 'required',
            'anyo' => 'required',
            'presupuesto_inicial' => 'required|min:0|max:9999999999',
            'presupuesto_restante' => 'required|min:0|max:9999999999',
        ]);
    }
}
