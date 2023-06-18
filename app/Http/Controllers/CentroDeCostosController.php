<?php

namespace App\Http\Controllers;

use App\Models\CentroDeCostos;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CentroDeCostosController extends Controller
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
        $query = CentroDeCostos::where(function ($query) use ($filtro) {
                $query->where('nombre', 'like', '%' . $filtro . '%')
                    ->orWhere('mes_del', 'like', '%' . $filtro . '%')
                    ->orWhere('mes_al', 'like', '%' . $filtro . '%')
                    ->orWhere('anyo', 'like', '%' . $filtro . '%')
                    ->orWhere('nombre', 'like', '%' . $filtro . '%')
                    ->orWhere('presupuesto_inicial', 'like', '%' . $filtro . '%')
                    ->orWhere('presupuesto_restante', 'like', '%' . $filtro . '%');
            })
            ->orderBy($ordenarPor, $descendente ? 'asc' : 'desc');
            //->orderBy('id');
        // $query = CentroDeCostos::all()->orderBy('id');
        $tuplas = $query->count();

        // Obtener los datos de la página actual
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();

        //Mandamos los meses y no el id
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        foreach ($detalle as $element) {
            $element['mes_del'] = $meses[$element['mes_del']];
            $element['mes_al'] = $meses[$element['mes_al']];
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
        $datos = new CentroDeCostos();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $datos->mes_del = array_search($request->mes_del, $meses);
        $datos->mes_al = array_search($request->mes_al, $meses);
        $datos->anyo = $request->anyo;
        $datos->nombre = $request->nombre;
        $datos->presupuesto_inicial = $request->presupuesto_inicial;
        $datos->presupuesto_restante = $request->presupuesto_restante;
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
    public function show(CentroDeCostos $centroDeCostos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CentroDeCostos $centroDeCostos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $this->validacion($request);
        $datos = CentroDeCostos::find($request->id);
        $user = User::find($request->user_id);

        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        // Verificar cada atributo y guardar el valor anterior si ha cambiado
        if ($datos->nombre != $request->nombre) {
            $atributosCambiados['nombre'] = [
                'anterior' => $datos->nombre,
                'actual' => $request->nombre,
            ];
        }
        if ($datos->anyo != $request->anyo) {
            $atributosCambiados['anyo'] = [
                'anterior' => $datos->anyo,
                'actual' => $request->anyo,
            ];
        }
        if ($datos->presupuesto_inicial != $request->presupuesto_inicial) {
            $atributosCambiados['presupuesto_inicial'] = [
                'anterior' => $datos->presupuesto_inicial,
                'actual' => $request->presupuesto_inicial,
            ];
        }
        if ($datos->presupuesto_restante != $request->presupuesto_restante) {
            $atributosCambiados['presupuesto_restante'] = [
                'anterior' => $datos->presupuesto_restante,
                'actual' => $request->presupuesto_restante,
            ];
        }
        if ($datos->mes_del != $request->mes_del) {
            $atributosCambiados['mes_del'] = [
                'anterior' => $datos->mes_del,
                'actual' => $request->mes_del,
            ];
        }
        if ($datos->mes_al != $request->mes_al) {
            $atributosCambiados['mes_al'] = [
                'anterior' => $datos->mes_al,
                'actual' => $request->mes_al,
            ];
        }


        $datos->mes_del = array_search($request->mes_del, $meses);
        $datos->mes_al = array_search($request->mes_al, $meses);
        $datos->nombre = $request->nombre;
        $datos->anyo = $request->anyo;
        $datos->presupuesto_inicial = $request->presupuesto_inicial;
        $datos->presupuesto_restante = $request->presupuesto_restante;
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
        $datos = CentroDeCostos::find($request->id);
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

    public function centro_de_costos()
    {
        $centro_de_costos = CentroDeCostos::select('id', 'nombre as name')->get();
        return response()->json(['centro_de_costos' => $centro_de_costos], 200);
    }

    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'mes_del' => 'required',
            'mes_al' => 'required',
            'anyo' => 'required|integer|min:1999|max:2099',
            'presupuesto_inicial' => 'required|integer|min:1000|max:9999999',
            //'presupuesto_restante' => 'required|integer|min:0|max:' . $request->presupuesto_inicial,
            'presupuesto_restante' => 'required|integer|min:0',
            'nombre' => 'required|max:150'
        ]);
    }

    public function obtenerNombre(Request $request)
    {
        $nombre = CentroDeCostos::select('nombre')->where('id', '=', $request->id)->get();

        return response()->json($nombre, 200);
    }
}
