<?php

namespace App\Http\Controllers;

use App\Models\Ocupaciones;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

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
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending;

        $query = Ocupaciones::where(function ($query) use ($filtro) {
            $query->where('nombre', 'like', '%' . $filtro . '%');
        })
            ->orderBy($ordenarPor, $descendente ? 'asc' : 'desc');

        $tuplas = $query->count();

        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();

        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro,
            'ordenarPor' => $ordenarPor
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

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($objeto)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
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


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'nombre',
        ];

        foreach ($atributos as $atributo) {
            if ($objeto->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $objeto->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $objeto->nombre = $request->nombre;
        $objeto->save();


        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($objeto)
                    ->withProperties([
                        'atributo' => $atributo,
                        'valor_anterior' => $valorAnterior,
                        'valor_actual' => $valorActual,
                    ])
                    ->log("Actualización");
                //->log("Editado el atributo '$atributo'. Valor anterior: '$valorAnterior'. Valor actual: '$valorActual'");
            }

            $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
            $lastActivity->causer; // Retorna el modelo que causó la actividad

            $atributoCambiado = $lastActivity->properties['atributo']; // Obtener el atributo cambiado
            $valorAnterior = $lastActivity->properties['valor_anterior']; // Obtener el valor anterior del atributo
            $valorActual = $lastActivity->properties['valor_actual']; // Obtener el valor actual del atributo
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $objeto = Ocupaciones::find($request->id);
        $objeto->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($objeto)
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
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
