<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentosController extends Controller
{
    public function TablaDepartamentos(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Departamento::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
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
    public function AgregarDepartamentos(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $departamentos = new Departamento();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $departamentos->nombre = $request->nombre;
        $departamentos->codigo_iso = $request->codigo_iso;
        // Guardando la informacion
        $departamentos->save();
    }
    // La operación de Update CR[U]D
    public function ActualizarDepartamentos(Request $request)
    {
        $this->validacion($request);
        $departamentos = Departamento::find($request->id);
        $departamentos->nombre = $request->nombre;
        $departamentos->codigo_iso = $request->codigo_iso;
        $departamentos->save();
    }
    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarDepartamentos(Request $request)
    {
        $departamentos = Departamento::find($request->id);
        $departamentos->delete();
    }
    /* METODOS INTERNOS con camelPascal */
    // Validacion de campos con Laravel
    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'nombre' => 'required|max:30',
            'codigo_iso' => 'required|between:0,5',
        ]);
    }

    //Consulta a departamentos
    public function ConsultarDepartamentos()
    {
        $departamentos = Departamento::all();
        return response()->json($departamentos);
    }
}