<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipiosController extends Controller
{
    public function TablaMunicipios(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        //$query = Municipio::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $query = Municipio::select('municipios.*', 'departamentos.nombre AS nombre_departamento')
                   ->join('departamentos', 'municipios.departamento_id', '=', 'departamentos.id')
                   ->where('municipios.nombre', 'like', '%' . $filtro . '%')
                   ->orderBy('municipios.id');
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
    }
    // La operación de Update CR[U]D
    public function ActualizarMunicipios(Request $request)
    {
        $this->validacion($request);
        $municipios = Municipio::find($request->id);
        $municipios->nombre = $request->nombre;
        $municipios->departamento_id = $request->departamento_id;
        $municipios->save();
    }
    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarMunicipios(Request $request)
    {
        $municipios = Municipio::find($request->id);
        $municipios->delete();
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
        //$municipios = Municipio::all();
        $municipios = Municipio::where('departamento_id', $departamento_id)->get();
        return response()->json($municipios);
    }
}
