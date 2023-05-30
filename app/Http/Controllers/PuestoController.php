<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PuestoController extends Controller
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
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        // $query = Puesto::select('id,n')->where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $query = DB::table('puestos as p2')
            ->leftJoin('puestos as p1', 'p2.superior_id', '=', 'p1.id')
            ->select('p2.id', 'p2.nombre', 'p2.nro_plazas', 'p2.salario_desde', 'p2.salario_hasta', 'p2.superior_id', 'p1.nombre as superior_nombre',)
            ->where('p2.nombre', 'like', '%' . $filtro . '%')
            ->orderBy('p2.id');
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
        $puesto = new Puesto();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $puesto->nombre = $request->nombre;
        $puesto->nro_plazas = $request->nro_plazas;
        $puesto->salario_desde = $request->salario_desde;
        $puesto->salario_hasta = $request->salario_hasta;
        $puesto->superior_id = $request->superior_id;
        // Guardando la informacion
        $puesto->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Puesto $puesto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Puesto $puesto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validacion($request);
        $puesto = Puesto::find($request->id);
        $puesto->nombre = $request->nombre;
        $puesto->nro_plazas = $request->nro_plazas;
        $puesto->salario_desde = $request->salario_desde;
        $puesto->salario_hasta = $request->salario_hasta;
        $puesto->superior_id = $request->superior_id;
        $puesto->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $puesto = Puesto::find($request->id);
        $puesto->delete();
    }

    public function consultarPuestos()
    {
        $superior = Puesto::select('id', 'nombre as name')->get();
        return response()->json($superior, 200);
    }

    public function consultarSuperiores(Request $request)
    {
        $ids = Puesto::select('superior_id')->whereNotNull('superior_id')->groupBy('superior_id')->get();
        $superiores = [];

        foreach ($ids as $element) {
            array_push($superiores, Puesto::select('id', 'nombre as name')->where('id', $element->superior_id)->first());
        }

        return response()->json(['superiores' => $superiores], 200);
    }

    public function consultar_id_nombre()
    {
        $datos = Puesto::select('id', 'nombre as name')->get();
        return response()->json($datos, 200);
    }

    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'nombre' => 'required|max:100',
            'nro_plazas' => 'required|integer|between:0,300',
            'salario_desde' => [
                'required',
                'numeric',
                'between:365,5000',
            ],
            'salario_hasta' => [
                'required',
                'numeric',
                'between:' . $request->salario_desde . ',5000',
            ],
        ]);
    }
}
