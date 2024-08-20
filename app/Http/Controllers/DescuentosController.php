<?php

namespace App\Http\Controllers;

use App\Models\Descuento;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class DescuentosController extends Controller
{
    public function TablaDescuentos(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $query = Descuento::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
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
    public function AgregarDescuentos(Request $request)
    {
        $this->validacion($request);
        $descuentos = new Descuento();
        $descuentos->nombre = $request->nombre;
        $descuentos->descripcion = $request->descripcion;
        $descuentos->forma_aplicacion = $request->forma_aplicacion;
        $descuentos->obligatorio = $request->obligatorio;
        $descuentos->valor_porcentaje = $request->valor_porcentaje;
        $descuentos->save();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($descuentos)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de Update CR[U]D
    public function ActualizarDescuentos(Request $request)
    {
        $this->validacion($request);
        $descuentos = Descuento::find($request->id);


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
            if ($descuentos->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $descuentos->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $descuentos->nombre = $request->nombre;
        $descuentos->descripcion = $request->descripcion;
        $descuentos->forma_aplicacion = $request->forma_aplicacion;
        $descuentos->obligatorio = $request->obligatorio;
        $descuentos->valor_porcentaje = $request->valor_porcentaje;
        $descuentos->save();

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

    // La operación de Delete CR[U]D
    public function EliminarDescuentos(Request $request)
    {
        $descuentos = Descuento::find($request->id);
        $descuentos->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($descuentos)
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
