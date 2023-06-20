<?php

namespace App\Http\Controllers;

use App\Models\CentroDeCostos;
use App\Models\Puesto;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class UnidadesController extends Controller
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
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending;
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        // $query = Unidades::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $query = DB::table('unidades as u1')
            ->leftJoin('unidades as u2', 'u1.nivel_organizacional', '=', 'u2.id')
            ->leftJoin('puestos as p', 'u1.superior_id', '=', 'p.id')
            ->leftJoin('centro_de_costos as c', 'u1.centro_costos_id', '=', 'c.id')
            ->select('u1.id', 'u1.nombre', 'u1.superior_id', 'u1.centro_costos_id', 'u1.nivel_organizacional', 'u2.nombre as nivel_organizacional_nombre', 'p.nombre as superior_nombre', 'c.anyo as centro_costos_año', 'c.nombre as centro_costos_nombre')
            ->where(function ($query) use ($filtro){
                $query->where('u1.nombre', 'like', '%'. $filtro. '%')
                ->orWhere('u2.nombre', 'like', '%' . $filtro . '%')
                ->orWhere('c.anyo', 'like', '%' . $filtro . '%')
                ->orWhere('c.nombre', 'like', '%' . $filtro . '%');
            })
            ->orderBy($ordenarPor, $descendente ? 'asc' : 'desc');
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
            'filtro' => $filtro,
            'ordenarPor' => $ordenarPor
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
        $datos = new Unidades();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $datos->nombre = $request->nombre;
        $datos->superior_id = $request->superior_id;
        $datos->centro_costos_id = $request->centro_de_costos;
        $datos->nivel_organizacional = $request->nivel_organizacional;
        // Guardando la informacion
        $datos->save();


        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($datos)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }

    /**
     * Display the specified resource.
     */
    public function show(Unidades $unidades)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unidades $unidades)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validacion($request);
        $datos = Unidades::find($request->id);


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'nombre',
            'descripcion',
            'forma_aplicacion',
            'obligatorio',
            'valor_porcentaje',
        ];

        foreach ($atributos as $atributo) {
            if ($datos->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $datos->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $datos->nombre = $request->nombre;
        $datos->superior_id = $request->superior_id;
        $datos->centro_costos_id = $request->centro_de_costos;
        $datos->nivel_organizacional = $request->nivel_organizacional;
        $datos->save();


        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($datos)
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
        $datos = Unidades::find($request->id);
        $datos->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($datos)
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }

    public function consultarUnidades()
    {
        $datos = Unidades::select('id', 'nombre as name')->get();
        return response()->json($datos, 200);
    }

    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100',
        ]);
    }
}
