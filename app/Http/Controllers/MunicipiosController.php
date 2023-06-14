<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class MunicipiosController extends Controller
{
    public function TablaMunicipios(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending; //true es descendente (mayor a menor) false es ascendente (menor a mayor). False default
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Municipio::select('municipios.*', 'departamentos.nombre AS nombre_departamento')
            ->join('departamentos', 'municipios.departamento_id', '=', 'departamentos.id')
            ->where(function ($query) use ($filtro) {
                $query->where('municipios.nombre', 'like', '%' . $filtro . '%')
                    ->orWhere('departamentos.nombre', 'like', '%' . $filtro . '%');
            })
            ->orderBy('municipios.nombre', $descendente ? 'desc' : 'asc');
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
    public function AgregarMunicipios(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $municipios = new Municipio();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $municipios->nombre = $request->nombre;
        $municipios->departamento_id = $request->departamento_id;
        // Guardando la informacion
        $municipios->save();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($municipios)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de Update CR[U]D
    public function ActualizarMunicipios(Request $request)
    {
        $this->validacion($request);
        $municipios = Municipio::find($request->id);


        $atributos = [
            'nombre',
            'departamento_id',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($municipios->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $municipios->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $municipios->nombre = $request->nombre;
        $municipios->departamento_id = $request->departamento_id;
        $municipios->save();


        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($municipios)
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
    public function EliminarMunicipios(Request $request)
    {
        $municipios = Municipio::find($request->id);
        $municipios->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($municipios)
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
            'nombre' => 'required|max:75',
            'departamento_id' => 'required|integer',
        ]);
    }

    //Consulta a departamentos
    public function ConsultarMunicipios($departamento_id)
    {
        $municipios = Municipio::where('departamento_id', $departamento_id)->get();
        // El json que se manda a la vista para poder visualizar la información
        return response()->json($municipios)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
