<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

use Illuminate\Http\Request;

class DireccionesController extends Controller
{
    public function TablaDirecciones(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]

        $query = Direccion::select('direccions.*', 'municipios.nombre AS nombre_municipio', 'departamentos.id AS departamento_id', 'departamentos.nombre AS departamento_nombre')
            ->join('municipios', 'direccions.municipio_id', '=', 'municipios.id')
            ->join('departamentos', 'municipios.departamento_id', '=', 'departamentos.id')
            ->where(function ($query) use ($filtro) {
                $query->where('direccions.calle', 'like', '%' . $filtro . '%')
                    ->orWhere('direccions.colonia', 'like', '%' . $filtro . '%')
                    ->orWhere('identificador_casa', 'like', '%' . $filtro . '%')
                    ->orWhere('apto_local', 'like', '%' . $filtro . '%')
                    ->orWhere('municipios.nombre', 'like', '%' . $filtro . '%');
            })
            ->orderBy('direccions.id');

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
    // La operación de Create [C]RUD
    public function AgregarDirecciones(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $direcciones = new Direccion();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $direcciones->calle = $request->calle;
        $direcciones->colonia = $request->colonia;
        $direcciones->identificador_casa = $request->identificador_casa;
        $direcciones->apto_local = $request->apto_local;
        $direcciones->municipio_id = $request->municipio_id;
        // Guardando la informacion
        $direcciones->save();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($direcciones)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de Update CR[U]D
    public function ActualizarDirecciones(Request $request)
    {
        $this->validacion($request);
        $direcciones = Direccion::find($request->id);


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'calle',
            'colonia',
            'identificador_casa',
            'apto_local',
            'municipio_id',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($direcciones->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $direcciones->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $direcciones->calle = $request->calle;
        $direcciones->colonia = $request->colonia;
        $direcciones->identificador_casa = $request->identificador_casa;
        $direcciones->apto_local = $request->apto_local;
        $direcciones->municipio_id = $request->municipio_id;
        $direcciones->save();

        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($direcciones)
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
    public function EliminarDirecciones(Request $request)
    {
        $direcciones = Direccion::find($request->id);
        $direcciones->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($direcciones)
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }

    public function consultar_id_nombre()
    {
        $datos = Direccion::select(DB::raw('id, identificador_casa || \', \' || apto_local || \', \' || calle || \', \' || colonia as name'))->get();
        return response()->json($datos, 200);
    }

    /* METODOS INTERNOS con camelPascal */
    // Validacion de campos con Laravel
    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'calle' => 'required|max:30',
            'colonia' => 'required|max:30',
            'identificador_casa' => 'required|max:30',
            'apto_local' => 'required|integer',
            'municipio_id' => 'required|integer',
        ]);
    }
}
