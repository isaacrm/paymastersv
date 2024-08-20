<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\EstadosCiviles;
=======
use App\Models\Estados_Civiles;
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class EstadosCivilesController extends Controller
{
<<<<<<< HEAD
    /* FUNCIONES PUBLICAS con PascalCase. Todas las variables con kebab_style */
    // Lo que se usara en la llamada asincrona para mostrar los datos en la tabla de la vista C[R]UD
    public function TablaEstadosCiviles(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending;
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
<<<<<<<< HEAD:app/Http/Controllers/EstadosCivilesController.php
        $query = EstadosCiviles::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
========
        $query = Departamento::where(function ($query) use ($filtro) {
            $query->where('nombre', 'like', '%' . $filtro . '%')
                ->orWhere('codigo_iso', 'like', '%' . $filtro . '%');
        })
            ->orderBy($ordenarPor, $descendente ? 'asc' : 'desc');
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/DepartamentosController.php
        $tuplas = $query->count();

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

        $query = Estados_Civiles::where(function ($query) use ($filtro) {
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
    public function AgregarEstadosCiviles(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $estados_civiles = new EstadosCiviles();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $estados_civiles->nombre = $request->nombre;
        
        // Guardando la informacion
<<<<<<<< HEAD:app/Http/Controllers/EstadosCivilesController.php
        $estados_civiles->save();
========
        $departamentos->save();
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
        $objeto = new Estados_Civiles();
        $objeto->nombre = $request->nombre;
        $objeto->save();
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
<<<<<<< HEAD
            ->performedOn($departamentos)
=======
            ->performedOn($objeto)
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
<<<<<<< HEAD
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/DepartamentosController.php
    }
    // La operación de Update CR[U]D
    public function ActualizarEstadosCiviles(Request $request)
    {
        $this->validacion($request);
<<<<<<<< HEAD:app/Http/Controllers/EstadosCivilesController.php
        $estados_civiles = EstadosCiviles::find($request->id);
        $estados_civiles->nombre = $request->nombre;
        $estados_civiles->save();
========
        $departamentos = Departamento::find($request->id);
=======
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validacion($request);
        $datos = Estados_Civiles::find($request->id);
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'nombre',
<<<<<<< HEAD
            'codigo_iso',
=======
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
<<<<<<< HEAD
            if ($departamentos->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $departamentos->$atributo,
=======
            if ($datos->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $datos->$atributo,
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
                    'actual' => $request->$atributo,
                ];
            }
        }


<<<<<<< HEAD
        $departamentos->nombre = $request->nombre;
        $departamentos->codigo_iso = $request->codigo_iso;
        $departamentos->save();

=======
        $datos->nombre = $request->nombre;
        $datos->save();
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8

        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
<<<<<<< HEAD
                    ->performedOn($departamentos)
=======
                    ->performedOn($datos)
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
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/DepartamentosController.php
    }

    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarEstadosCiviles(Request $request)
    {
<<<<<<<< HEAD:app/Http/Controllers/EstadosCivilesController.php
        $estados_civiles = EstadosCiviles::find($request->id);
        $estados_civiles->delete();
========
        $departamentos = Departamento::find($request->id);
        $departamentos->delete();
=======
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $datos = Estados_Civiles::find($request->id);
        $datos->delete();
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
<<<<<<< HEAD
            ->performedOn($departamentos)
=======
            ->performedOn($datos)
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
<<<<<<< HEAD
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/DepartamentosController.php
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
<<<<<<<< HEAD:app/Http/Controllers/EstadosCivilesController.php
}
========

    //Consulta a departamentos
    public function ConsultarDepartamentos()
    {
        $departamentos = Departamento::all();
        return response()->json($departamentos);
    }
}
>>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:app/Http/Controllers/DepartamentosController.php
=======
    }

    public function consultar_id_nombre()
    {
        $datos = Estados_Civiles::select('id', 'nombre as name')->get();
        return response()->json($datos, 200);
    }

    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:30',
        ]);
    }
}
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
