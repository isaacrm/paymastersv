<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class IngresosController extends Controller
{
    public function TablaIngresos(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $query = Ingreso::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $tuplas = $query->count();
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();
        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro
        ];
        return response()->json([
            'detalle' => $detalle,
            'paginacion' => $paginacion,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    // La operación de Create [C]RUD
    public function AgregarIngresos(Request $request)
    {
        $this->validacion($request);
        $ingresos = new Ingreso();
        $ingresos->nombre = $request->nombre;
        $ingresos->descripcion = $request->descripcion;
        $ingresos->forma_aplicacion = $request->forma_aplicacion;
        $ingresos->obligatorio = $request->obligatorio;
        $ingresos->valor_porcentaje = $request->valor_porcentaje;
        $ingresos->save();

        //echo '<pre>';
        //var_dump($ingresos); // Imprime el contenido del objeto $ingresos
        //echo '</pre>';
        
        //Bitacora
        $user = User::find($request->user_id);
        activity()
        ->causedBy($user)
        ->performedOn($ingresos)
        ->log("Creación");
        
        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
        exit();
    }
    // La operación de Update CR[U]D
    public function ActualizarIngresos(Request $request)
    {
        $this->validacion($request);
        $ingresos = Ingreso::find($request->id);

        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'nombre',
            'descripcion',
            'forma_aplicacion',
            'obligatorio',
            'valor_porcentaje',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($ingresos->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $ingresos->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }

        $ingresos->nombre = $request->nombre;
        $ingresos->descripcion = $request->descripcion;
        $ingresos->forma_aplicacion = $request->forma_aplicacion;
        $ingresos->obligatorio = $request->obligatorio;
        $ingresos->valor_porcentaje = $request->valor_porcentaje;
        $ingresos->save();

        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($ingresos)
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
    // La operación de Delete CR[U]D
    public function EliminarIngresos(Request $request)
    {
        $ingresos = Ingreso::find($request->id);
        $ingresos->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($ingresos)
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de validación
    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:30',
            'descripcion' => 'required|max:100',
            'forma_aplicacion' => 'required|max:1',
            'obligatorio' => 'required',
            'valor_porcentaje' => 'between:0,100',
        ]);
    }
}
