<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class EmpresasController extends Controller
{
    public function TablaEmpresas(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending;
        $query = Empresa::where(function($query) use ($filtro){
            $query->where('nombre', 'like', '%' . $filtro . '%')
            ->orWhere('nit', 'like', '%' . $filtro . '%')
            ->orWhere('telefono', 'like', '%' . $filtro . '%')
            ->orWhere('nrc', 'like', '%' . $filtro . '%')
            ->orWhere('email', 'like', '%' . $filtro . '%')
            ->orWhere('sitio_web', 'like', '%' . $filtro . '%')
            ->orWhere('numero_patronal', 'like', '%' . $filtro . '%')
            ->orWhere('representante_legal', 'like', '%' . $filtro . '%');
        })
        ->orderBy($ordenarPor, $descendente ? 'asc' : 'desc');

        $tuplas = $query->count();
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();
        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro,
            'ordenarPor' => $ordenarPor
        ];
        return response()->json([
            'detalle' => $detalle,
            'paginacion' => $paginacion,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    // La operación de Create [C]RUD
    public function AgregarEmpresas(Request $request)
    {
        $this->validacion($request);
        $empresas = new Empresa();
        $empresas->nombre = $request->nombre;
        $empresas->nit = $request->nit;
        $empresas->telefono = $request->telefono;
        $empresas->nrc = $request->nrc;
        $empresas->email = $request->email;
        $empresas->sitio_web = $request->sitio_web;
        $empresas->numero_patronal = $request->numero_patronal;
        $empresas->representante_legal = $request->representante_legal;
        $empresas->save();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($empresas)
            ->log("Creación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de Update CR[U]D
    public function ActualizarEmpresas(Request $request)
    {
        $this->validacion($request);
        $empresas = Empresa::find($request->id);


        $atributosCambiados = []; // Array para almacenar los atributos que han cambiado

        $atributos = [
            'nombre',
            'nit',
            'telefono',
            'nrc',
            'email',
            'sitio_web',
            'numero_patronal',
            'representante_legal',
        ];

        $atributosCambiados = [];

        foreach ($atributos as $atributo) {
            if ($empresas->$atributo != $request->$atributo) {
                $atributosCambiados[$atributo] = [
                    'anterior' => $empresas->$atributo,
                    'actual' => $request->$atributo,
                ];
            }
        }


        $empresas->nombre = $request->nombre;
        $empresas->nit = $request->nit;
        $empresas->telefono = $request->telefono;
        $empresas->nrc = $request->nrc;
        $empresas->email = $request->email;
        $empresas->sitio_web = $request->sitio_web;
        $empresas->numero_patronal = $request->numero_patronal;
        $empresas->representante_legal = $request->representante_legal;
        $empresas->save();


        $user = User::find($request->user_id);
        if ($atributosCambiados != []) {
            foreach ($atributosCambiados as $atributo => $valores) {
                $valorAnterior = $valores['anterior'];
                $valorActual = $valores['actual'];

                activity()
                    ->causedBy($user)
                    ->performedOn($empresas)
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
    public function EliminarEmpresas(Request $request)
    {
        $empresas = Empresa::find($request->id);
        $empresas->delete();

        //Bitacora
        $user = User::find($request->user_id);
        activity()
            ->causedBy($user)
            ->performedOn($empresas)
            ->log("Eliminación");

        $lastActivity = Activity::all()->last(); // Retorna la última actividad registrada
        $lastActivity->causer; // Retorna el modelo que causó la actividad
    }
    // La operación de validación
    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:30',
            'nit' => ['required', 'regex:/^\d{4}-\d{6}-\d{3}-\d$/'],
            'telefono' => ['required', 'numeric', 'digits_between:1,8', 'regex:/^[267][0-8]*$/'],
            'nrc' => 'required|max:7',
            'email' => 'required|email|max:75',
            'sitio_web' => 'required|max:250',
            'numero_patronal' => ['required', 'numeric', 'digits:9'],
            'representante_legal' => 'required|max:250',
        ]);
    }
}
