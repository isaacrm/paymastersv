<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RentasMensuale;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class RentaMensualController extends Controller
{
    /* FUNCIONES PUBLICAS con PascalCase. Todas las variables con kebab_style */
    // Lo que se usara en la llamada asincrona para mostrar los datos en la tabla de la vista C[R]UD
    public function TablaRentaMensual(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending; //true es descendente (mayor a menor) false es ascendente (menor a mayor). False default
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = RentasMensuale::select('rentas_mensuales.*', DB::raw('porcentaje_aplicar * 100 AS porcentaje'))
            ->where('tramo', 'like', '%' . $filtro . '%')
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
    public function AgregarRentaMensual(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $renta_mensual = new RentasMensuale();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $renta_mensual->tramo = $request->tramo;
        $renta_mensual->desde = $request->desde;
        $renta_mensual->hasta = $request->hasta;
        $porcentaje = $request->porcentaje_aplicar / 100;
        $renta_mensual->porcentaje_aplicar = $porcentaje;
        $renta_mensual->sobre_exceso = $request->sobre_exceso;
        $renta_mensual->mas_fija = $request->mas_fija;
        // Guardando la informacion
        $renta_mensual->save();


        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($renta_mensual)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de Update CR[U]D
    public function ActualizarRentaMensual(Request $request)
    {
        $this->validacion($request);
        $renta_mensual = RentasMensuale::find($request->id);


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'tramo',
            'desde',
            'hasta',
            'porcentaje_aplicar',
            'sobre_exceso',
            'mas_fija',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($renta_mensual->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $renta_mensual->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $renta_mensual->tramo = $request->tramo;
        $renta_mensual->desde = $request->desde;
        $renta_mensual->hasta = $request->hasta;
        $porcentaje = $request->porcentaje_aplicar / 100;
        $renta_mensual->porcentaje_aplicar = $porcentaje;
        $renta_mensual->sobre_exceso = $request->sobre_exceso;
        $renta_mensual->mas_fija = $request->mas_fija;
        $renta_mensual->save();


        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($renta_mensual)
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
    public function EliminarRentaMensual(Request $request)
    {
        $renta_mensual = RentasMensuale::find($request->id);
        $renta_mensual->delete();


        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($renta_mensual)
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
            'tramo' => ['required', 'integer', 'between:0,6', Rule::unique('rentas_mensuales')->ignore($request->id)],
            'desde' => 'required|numeric|between:0,9999.99',
            'hasta' => 'required|numeric|between:0,9999.99',
            'porcentaje_aplicar' => 'required|numeric|between:0,100',
            'sobre_exceso' => 'required|numeric|between:0,9999.99',
            'mas_fija' => 'required|numeric|between:0,9999.99',
        ]);
    }
}
