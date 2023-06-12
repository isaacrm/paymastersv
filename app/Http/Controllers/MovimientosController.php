<?php

namespace App\Http\Controllers;

use App\Models\CentroDeCostos;
use App\Models\Movimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        //Actualizamos el monto de movimientos
        $centro_de_costo_foraneo = CentroDeCostos::find($request->centro_costos_id);
        $centro_de_costo_foraneo->presupuesto_restante = $centro_de_costo_foraneo->presupuesto_restante + $datos->monto;

        // Guardamos
        $centro_de_costo_foraneo->save();
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

        //Obtemos el centro de costo padre del movimiento
        $centro_de_costo_foraneo = CentroDeCostos::find($request->centro_costos_id);

        $this->validacion($request);
        $datos = Movimientos::find($request->id);
        $user = User::find($request->user_id);


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        // Verificar cada atributo y guardar el valor anterior si ha cambiado
        if ($datos->monto != $request->monto) {
            $atributosCambiados['monto'] = [
                'anterior' => $datos->monto,
                'actual' => $request->monto,
            ];
            $datos->monto = $request->monto;
        }
        // Verificar cada atributo y guardar el valor anterior si ha cambiado
        if ($datos->descripcion != $request->descripcion) {
            $atributosCambiados['descripcion'] = [
                'anterior' => $datos->descripcion,
                'actual' => $request->descripcion,
            ];
            $datos->descripcion = $request->descripcion;
        }


        //Actualizamos el monto del centro de costo
        $centro_de_costo_foraneo->presupuesto_restante -= $datos->monto;
        $centro_de_costo_foraneo->presupuesto_restante += $request->monto;

        //Actualizamos los campos del movimiento
        $datos->descripcion = $request->descripcion;
        $datos->centro_costos_id = $request->centro_costos_id;
        $datos->operacion = '+';

        // Ajustamos el presupuesto

        // * Asignamos el nuevo monto de movimiento
        $datos->monto = $request->monto;


        if ($request->planillas_id) {
            $datos->planillas_id = $request->planillas_id;
        }

        // Guardamos
        $centro_de_costo_foraneo->save();
        $datos->save();

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
        $datos = Movimientos::find($request->id);

        //Actualizamos el monto de movimientos
        $centro_de_costo_foraneo = CentroDeCostos::find($datos->centro_costos_id);
        $centro_de_costo_foraneo->presupuesto_restante = $centro_de_costo_foraneo->presupuesto_restante - $datos->monto;

        $centro_de_costo_foraneo->save();
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

    private function validacion(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|max:150',
            'monto' => 'required|integer|min:0',
            'centro_costos_id' => 'required'
        ]);
    }
}
