<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class PuestoController extends Controller
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
        // $query = Puesto::select('id,n')->where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $query = DB::table('puestos as p2')
            ->leftJoin('puestos as p1', 'p2.superior_id', '=', 'p1.id')
            ->select('p2.id', 'p2.nombre', 'p2.nro_plazas', 'p2.salario_desde', 'p2.salario_hasta', 'p2.superior_id', 'p1.nombre as superior_nombre',)
            ->where(function ($query) use ($filtro) {
                $query->where('p2.nombre', 'like', '%' . $filtro . '%')
                    ->orWhere('p2.nro_plazas', 'like', '%' . $filtro . '%')
                    ->orWhere('p2.salario_desde', 'like', '%' . $filtro . '%')
                    ->orWhere('p2.salario_hasta', 'like', '%' . $filtro . '%')
                    ->orWhere('p1.nombre', 'like', '%' . $filtro . '%');
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
        $puesto = new Puesto();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $puesto->nombre = $request->nombre;
        $puesto->nro_plazas = $request->nro_plazas;
        $puesto->salario_desde = $request->salario_desde;
        $puesto->salario_hasta = $request->salario_hasta;
        $puesto->superior_id = $request->superior_id;
        // Guardando la informacion
        $puesto->save();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($puesto)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
        exit();
    }

    /**
     * Display the specified resource.
     */
    public function show(Puesto $puesto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Puesto $puesto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validacion($request);
        $puesto = Puesto::find($request->id);


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'nombre',
            'nro_plazas',
            'salario_desde',
            'salario_hasta',
            'superior_id',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($puesto->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $puesto->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $puesto->nombre = $request->nombre;
        $puesto->nro_plazas = $request->nro_plazas;
        $puesto->salario_desde = $request->salario_desde;
        $puesto->salario_hasta = $request->salario_hasta;
        $puesto->superior_id = $request->superior_id;
        $puesto->save();



        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($puesto)
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
        $puesto = Puesto::find($request->id);
        $puesto->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($puesto)
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }

    public function consultarPuestos()
    {
        $superior = Puesto::select('id', 'nombre as name')->get();
        return response()->json($superior, 200);
    }

    public function consultarSuperiores(Request $request)
    {
        $ids = Puesto::select('superior_id')->whereNotNull('superior_id')->groupBy('superior_id')->get();
        $superiores = [];

        foreach ($ids as $element) {
            array_push($superiores, Puesto::select('id', 'nombre as name')->where('id', $element->superior_id)->first());
        }

        return response()->json(['superiores' => $superiores], 200);
    }

    public function consultar_id_nombre()
    {
        $datos = Puesto::select('id', 'nombre as name')->get();
        return response()->json($datos, 200);
    }

    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'nombre' => 'required|max:100',
            'nro_plazas' => 'required|integer|between:0,300',
            'salario_desde' => [
                'required',
                'numeric',
                'between:365,5000',
            ],
            'salario_hasta' => [
                'required',
                'numeric',
                'between:' . $request->salario_desde . ',5000',
            ],
        ]);
    }
}
