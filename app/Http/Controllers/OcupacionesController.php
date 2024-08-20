<?php

namespace App\Http\Controllers;

use App\Models\Ocupaciones;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class OcupacionesController extends Controller
{
<<<<<<< HEAD
    /* FUNCIONES PUBLICAS con PascalCase. Todas las variables con kebab_style */
    // Lo que se usara en la llamada asincrona para mostrar los datos en la tabla de la vista C[R]UD
    public function TablaOcupaciones(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending; //true es descendente (mayor a menor) false es ascendente (menor a mayor). False default
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
<<<<<<<< HEAD:app/Http/Controllers/OcupacionesController.php
        $query = Ocupaciones::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $tuplas = $query->count();
========
        $query = Municipio::select('municipios.*', 'departamentos.nombre AS nombre_departamento')
            ->join('departamentos', 'municipios.departamento_id', '=', 'departamentos.id')
            ->where(function ($query) use ($filtro) {
                $query->where('municipios.nombre', 'like', '%' . $filtro . '%')
                    ->orWhere('departamentos.nombre', 'like', '%' . $filtro . '%');
            })
            ->orderBy('municipios.nombre', $descendente ? 'desc' : 'asc');
            $tuplas = $query->count();
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/MunicipiosController.php

        // Obtener los datos de la página actual
=======
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

>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();

<<<<<<< HEAD
        // Informacion pertinente a la paginacion para llamarlos en la vista
=======
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
<<<<<<< HEAD
            'filtro' => $filtro,
            'ordenarPor' => $ordenarPor

        ];

        // El json que se manda a la vista para poder visualizar la información
=======
            'filtro' => $filtro
        ];

>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
        return response()->json([
            'detalle' => $detalle,
            'paginacion' => $paginacion,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

<<<<<<< HEAD
    // La operación de Create [C]RUD
    public function AgregarOcupaciones(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $ocupaciones = new Ocupaciones();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $ocupaciones->nombre = $request->nombre;
        // Guardando la informacion
<<<<<<<< HEAD:app/Http/Controllers/OcupacionesController.php
        $ocupaciones->save();
========
        $municipios->save();
=======
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
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
<<<<<<< HEAD
            ->performedOn($municipios)
=======
            ->performedOn($objeto)
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
<<<<<<< HEAD
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/MunicipiosController.php
    }
    // La operación de Update CR[U]D
    public function ActualizarOcupaciones(Request $request)
    {
        $this->validacion($request);
<<<<<<<< HEAD:app/Http/Controllers/OcupacionesController.php
        $ocupaciones = Ocupaciones::find($request->id);
        $ocupaciones->nombre = $request->nombre;
        $ocupaciones->save();
========
        $municipios = Municipio::find($request->id);


        $atributos = [
            'nombre',
            'departamento_id',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($municipios->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $municipios->$atributo,
=======
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
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
                    'actual' => $request->$atributo,
                ];
            }
        }


<<<<<<< HEAD
        $municipios->nombre = $request->nombre;
        $municipios->departamento_id = $request->departamento_id;
        $municipios->save();
=======
        $objeto->nombre = $request->nombre;
        $objeto->save();
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8


        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
<<<<<<< HEAD
                    ->performedOn($municipios)
=======
                    ->performedOn($objeto)
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
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
<<<<<<< HEAD
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/MunicipiosController.php
    }

    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarOcupaciones(Request $request)
    {
<<<<<<<< HEAD:app/Http/Controllers/OcupacionesController.php
        $ocupaciones = Ocupaciones::find($request->id);
        $ocupaciones->delete();
========
        $municipios = Municipio::find($request->id);
        $municipios->delete();
=======
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $objeto = Ocupaciones::find($request->id);
        $objeto->delete();
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
<<<<<<< HEAD
            ->performedOn($municipios)
=======
            ->performedOn($objeto)
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
<<<<<<< HEAD
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/MunicipiosController.php
    }

    /* METODOS INTERNOS con camelPascal */
    // Validacion de campos con Laravel
    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'nombre' => 'required|max:75',
        ]);
    }
<<<<<<<< HEAD:app/Http/Controllers/OcupacionesController.php
}
========

    //Consulta a departamentos
    public function ConsultarMunicipios($departamento_id)
    {
        $municipios = Municipio::where('departamento_id', $departamento_id)->get();
        // El json que se manda a la vista para poder visualizar la información
        return response()->json($municipios)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/MunicipiosController.php
=======
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
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
