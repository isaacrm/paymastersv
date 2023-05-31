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
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Planillas::where('id', 'like', '%' . $filtro . '%')->orderBy('id');
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
        $datos = new Planillas();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $datos->mes_periodo = array_search($request->mes_periodo, $meses);
        $datos->anyo_periodo = $request->anyo_periodo;
        $datos->fecha_generacion = $request->fecha_generacion;
        $datos->dias_laborales = $request->dias_laborales;
        $datos->horas_laborales = $request->horas_laborales;
        $datos->save();

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
        $datos->mes_periodo = array_search($request->mes_periodo, $meses);
        $datos->anyo_periodo = $request->anyo_periodo;
        $datos->fecha_generacion = $request->fecha_generacion;
        $datos->dias_laborales = $request->dias_laborales;
        $datos->horas_laborales = $request->horas_laborales;
        $datos->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $datos = Planillas::find($request->id);
        $datos->delete();
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
