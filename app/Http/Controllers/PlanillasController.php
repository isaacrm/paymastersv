<?php

namespace App\Http\Controllers;

use App\Models\Planillas;
use App\Models\Descuento;
use App\Models\Empleados;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class PlanillasController extends Controller
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
        $query = Planillas::where(function ($query) use ($filtro) {
            $query->where('fecha_generacion', 'like', '%' . $filtro . '%')
                ->orWhere('mes_periodo', 'like', '%' . $filtro . '%')
                ->orWhere('anyo_periodo', 'like', '%' . $filtro . '%')
                ->orWhere('dias_laborales', 'like', '%' . $filtro . '%')
                ->orWhere('horas_laborales', 'like', '%' . $filtro . '%');
        })->orderBy($ordenarPor, $descendente ? 'asc' : 'desc');

        // $query = CentroDeCostos::all()->orderBy('id');
        $tuplas = $query->count();

        // Obtener los datos de la página actual
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();

        //Mandamos los meses y no el id
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        foreach ($detalle as $element) {
            $element['mes_periodo'] = $meses[$element['mes_periodo']];
        }

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
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $datos = new Planillas();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $datos->mes_periodo = array_search($request->mes_periodo, $meses);
        $datos->anyo_periodo = $request->anyo_periodo;
        $datos->fecha_generacion = $request->fecha_generacion;
        $datos->dias_laborales = $request->dias_laborales;
        $datos->horas_laborales = $request->horas_laborales;
        $datos->save();


        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($datos)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad

        // Para ejecutar el procedimiento almacenado
        DB::statement('BEGIN pa_detalle_planillas(:p_planilla_id); END;', ['p_planilla_id' => $datos->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Planillas $planillas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Planillas $planillas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Planillas $planillas)
    {
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $datos = Planillas::find($request->id);
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request

        $atributos = [
            'mes_periodo',
            'anyo_periodo',
            'fecha_generacion',
            'dias_laborales',
            'horas_laborales',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($datos->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $datos->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }

        $datos->mes_periodo = array_search($request->mes_periodo, $meses);
        $datos->anyo_periodo = $request->anyo_periodo;
        $datos->fecha_generacion = $request->fecha_generacion;
        $datos->dias_laborales = $request->dias_laborales;
        $datos->horas_laborales = $request->horas_laborales;
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
        $datos = Planillas::find($request->id);
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

    public function CantidadRegistros()
    {
        $cantidadDescuentos = Descuento::where('obligatorio', '=', 'S')->count();
        $cantidadEmpleados = Empleados::count();
        // El json que se manda a la vista para poder visualizar la información
        return response()->json([
            'descuentosExistentes' => $cantidadDescuentos,
            'empleadosExistentes' => $cantidadEmpleados,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function Redireccion(Request $request)
    {
        $id = $request->planillas_id;
        return Inertia::render('Planillas/DetallePlanillas', [
            'idPlanilla' => $id,
        ]);
    }

    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'mes_periodo' => 'required',
            'anyo_periodo' => 'required|integer|min:1999|max:2099',
            'fecha_generacion' => 'required',
            'dias_laborales' => 'required|integer|min:0|max:30',
            'horas_laborales' => 'required|integer|min:0|max:300',
        ]);
    }
}
