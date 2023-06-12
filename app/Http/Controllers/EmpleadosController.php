<?php

namespace App\Http\Controllers;

use App\Models\Empleados;
use App\Models\Puesto;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;


class EmpleadosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;

        /*         $query = Empleados::where(function ($query) use ($filtro) {
            $query->where('primer_nombre', 'like', '%' . $filtro . '%');
        })
            ->orderBy('id'); */

        $query = DB::table('empleados as e')
            ->select([
                'e.id', 'e.primer_nombre', 'e.segundo_nombre', 'e.apellido_paterno', 'e.apellido_materno', 'e.apellido_casada', 'e.identificacion', 'e.nit', 'e.isss', 'e.nup', 'e.email_personal', 'e.email_profesional', 'e.fecha_nacimiento', 'e.fecha_ingreso', 'e.salario_base',
                'ec.id as estado_civil_id', 'ec.nombre as estado_civil_nombre',
                'g.id as genero_id', 'g.nombre as genero_nombre',
                'o.id as ocupacion_id', 'o.nombre as ocupacion_nombre',
                'td.id as tipo_documento_id', 'td.nombre as tipo_documento_nombre',
                'd.id as direccion_id', 'd.calle  as direccion_nombre',
                'p.id as puesto_id', 'p.nombre as puesto_nombre',
            ])
            ->leftJoin('estados__civiles as ec', 'e.estados_civiles_id', '=', 'ec.id')
            ->leftJoin('generos as g', 'e.generos_id', '=', 'g.id')
            ->leftJoin('ocupaciones as o', 'e.ocupaciones_id', '=', 'o.id')
            ->leftJoin('tipo_documentos as td', 'e.tipo_documentos_id', '=', 'td.id')
            ->leftJoin('direccions as d', 'e.direcciones_id', '=', 'd.id')
            ->leftJoin('puestos as p', 'e.puestos_id', '=', 'p.id')
            ->where(function ($query) use ($filtro) {
                $query->where('e.primer_nombre', 'like', '%' . $filtro . '%')
                    ->orWhere('e.segundo_nombre', 'like', '%' . $filtro . '%')
                    ->orWhere('e.apellido_paterno', 'like', '%' . $filtro . '%')
                    ->orWhere('e.apellido_materno', 'like', '%' . $filtro . '%')
                    ->orWhere('e.apellido_casada', 'like', '%' . $filtro . '%')
                    ->orWhere('e.identificacion', 'like', '%' . $filtro . '%')
                    ->orWhere('e.nit', 'like', '%' . $filtro . '%')
                    ->orWhere('e.nup', 'like', '%' . $filtro . '%')
                    ->orWhere('e.fecha_nacimiento', 'like', '%' . $filtro . '%')
                    ->orWhere('e.email_personal', 'like', '%' . $filtro . '%')
                    ->orWhere('e.email_profesional', 'like', '%' . $filtro . '%');
            })
            ->orderBy('e.id');

        $result = $query->get();


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
        $this->validacion($request);

        $datos = new Empleados();

        $datos->primer_nombre = $request->primer_nombre;
        $datos->segundo_nombre = $request->segundo_nombre;
        $datos->apellido_paterno = $request->apellido_paterno;
        $datos->apellido_materno = $request->apellido_materno;
        $datos->apellido_casada = $request->apellido_casada;
        $datos->fecha_nacimiento = $request->fecha_nacimiento;
        $datos->fecha_ingreso = $request->fecha_ingreso;
        $datos->identificacion = $request->identificacion;
        $datos->nit = $request->nit;
        $datos->isss = $request->isss;
        $datos->nup = $request->nup;
        $datos->email_personal = $request->email_personal;
        $datos->email_profesional = $request->email_profesional;
        $datos->salario_base = $request->salario_base;
        $datos->estados_civiles_id = $request->estados_civiles_id;
        $datos->generos_id = $request->generos_id;
        $datos->ocupaciones_id = $request->ocupaciones_id;
        $datos->tipo_documentos_id = $request->tipo_documentos_id;
        $datos->direcciones_id = $request->direcciones_id;
        $datos->puestos_id = $request->puestos_id;

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
    public function show(Empleados $empleados)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empleados $empleados)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validacion($request);

        $datos = Empleados::find($request->id);

        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'primer_nombre',
            'segundo_nombre',
            'apellido_paterno',
            'apellido_materno',
            'apellido_casada',
            'fecha_nacimiento',
            'fecha_ingreso',
            'identificacion',
            'nit',
            'isss',
            'nup',
            'email_personal',
            'email_profesional',
            'salario_base',
            'estados_civiles_id',
            'generos_id',
            'ocupaciones_id',
            'tipo_documentos_id',
            'direcciones_id',
            'puestos_id',
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

        $datos->primer_nombre = $request->primer_nombre;
        $datos->segundo_nombre = $request->segundo_nombre;
        $datos->apellido_paterno = $request->apellido_paterno;
        $datos->apellido_materno = $request->apellido_materno;
        $datos->apellido_casada = $request->apellido_casada;
        $datos->fecha_nacimiento = $request->fecha_nacimiento;
        $datos->fecha_ingreso = $request->fecha_ingreso;
        $datos->identificacion = $request->identificacion;
        $datos->nit = $request->nit;
        $datos->isss = $request->isss;
        $datos->nup = $request->nup;
        $datos->email_personal = $request->email_personal;
        $datos->email_profesional = $request->email_profesional;
        $datos->salario_base = $request->salario_base;
        $datos->estados_civiles_id = $request->estados_civiles_id;
        $datos->generos_id = $request->generos_id;
        $datos->ocupaciones_id = $request->ocupaciones_id;
        $datos->tipo_documentos_id = $request->tipo_documentos_id;
        $datos->direcciones_id = $request->direcciones_id;
        $datos->puestos_id = $request->puestos_id;

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
        $datos = Empleados::find($request->id);
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
        $longitud_tipo_documentos = 25;
        if ($request->tipo_documentos_id) {
            $longitud_tipo_documentos = TipoDocumento::select('longitud')->where('id', '=', $request->tipo_documentos_id)->get();
            $longitud_tipo_documentos = $longitud_tipo_documentos[0]->longitud;
        }

        $salario_desde = 360;
        $salario_hasta = 6000;
        if ($request->puestos_id) {
            $salario_desde = Puesto::select('salario_desde')->where('id', '=', $request->puestos_id)->get();
            $salario_desde = $salario_desde[0]->salario_desde;
            $salario_hasta = Puesto::select('salario_hasta')->where('id', '=', $request->puestos_id)->get();
            $salario_hasta = $salario_hasta[0]->salario_hasta;
        }

        $fecha_de_hace_dieiciocho_años = date('Y-m-d', strtotime('-18 years'));

        $messages = [
            'fecha_nacimiento.before_or_equal' => 'El empleado debe ser mayor de 18 años para poder trabajar'
        ];
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'primer_nombre' => 'required|max:25',
            'segundo_nombre' => 'required|max:25',
            'apellido_paterno' => 'required|max:30',
            'apellido_materno' => 'required|max:30',
            'apellido_casada' => 'max:35',
            'fecha_nacimiento' => [
                'required',
                'before_or_equal:' . $fecha_de_hace_dieiciocho_años
            ],
            'fecha_ingreso' => 'required',
            'identificacion' => 'required|max:' . $longitud_tipo_documentos,
            'nit' => ['required', 'max:25', 'regex:/^\d{4}-\d{6}-\d{3}-\d$/'],
            'isss' => 'required|max:25',
            'nup' => 'required|max:20',
            'email_personal' =>  'required|email',
            'email_profesional' =>  'required|email',
            // 'salario_base' => 'required|decimal:2|min:365|max:9999',
            'salario_base' => 'required|numeric|min:' . $salario_desde . '|max:' . $salario_hasta,
            'estados_civiles_id' => 'required',
            'generos_id' => 'required',
            'ocupaciones_id' => 'required',
            'tipo_documentos_id' => 'required',
            'direcciones_id' => 'required',
            'puestos_id' => 'required',
        ], $messages);
    }
}
