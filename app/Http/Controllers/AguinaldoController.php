<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aguinaldo;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class AguinaldoController extends Controller
{
    /* FUNCIONES PUBLICAS con PascalCase. Todas las variables con kebab_style */
    // Lo que se usara en la llamada asincrona para mostrar los datos en la tabla de la vista C[R]UD
    public function TablaAguinaldo(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending; //true es descendente (mayor a menor) false es ascendente (menor a mayor). False default
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Aguinaldo::where('desde', 'like', '%' . $filtro . '%')
            ->orWhere('hasta', 'like', '%' . $filtro . '%')
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

    // La operación de Create [C]RUD
    public function AgregarAguinaldo(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $aguinaldo = new Aguinaldo();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $aguinaldo->desde = $request->desde;
        $aguinaldo->hasta = $request->hasta;
        $aguinaldo->cantidad_dias = $request->cantidad_dias;
        // Guardando la informacion
        $aguinaldo->save();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($aguinaldo)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de Update CR[U]D
    public function ActualizarAguinaldo(Request $request)
    {
        $this->validacion($request);
        $aguinaldo = Aguinaldo::find($request->id);


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'desde',
            'hasta',
            'cantidad_dias',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($aguinaldo->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $aguinaldo->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $aguinaldo->desde = $request->desde;
        $aguinaldo->hasta = $request->hasta;
        $aguinaldo->cantidad_dias = $request->cantidad_dias;
        $aguinaldo->save();



        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($aguinaldo)
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

    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarAguinaldo(Request $request)
    {
        $aguinaldo = Aguinaldo::find($request->id);
        $aguinaldo->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($aguinaldo)
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }

    /* METODOS INTERNOS con camelPascal */
    // Validacion de campos con Laravel
    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'desde' => 'required|integer|between:0,20',
            'hasta' => 'required|integer|between:0,100',
            'cantidad_dias' => 'integer|between:0,50',
        ]);
    }
}
