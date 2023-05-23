<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;

class GenerosController extends Controller
{
    /* FUNCIONES PUBLICAS con PascalCase. Todas las variables con kebab_style */
    // Lo que se usara en la llamada asincrona para mostrar los datos en la tabla de la vista C[R]UD
    public function TablaGeneros(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Genero::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
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
    public function AgregarGeneros(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Estableciendo el modelo donde se guardara la informacion
        $generos = new Genero();
        // Determinando que valor tendra cada atributo del modelo con lo que se obtiene con el request
        $generos->nombre = $request->nombre;
        
        // Guardando la informacion
        $generos->save();
    }
    // La operación de Update CR[U]D
    public function ActualizarGeneros(Request $request)
    {
        $this->validacion($request);
        $generos = Genero::find($request->id);
        $generos->nombre = $request->nombre;
        $generos->save();
    }

    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarGeneros(Request $request)
    {
        $generos = Genero::find($request->id);
        $generos->delete();
    }

    /* METODOS INTERNOS con camelPascal */
    // Validacion de campos con Laravel
    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'nombre' => 'required|max:75',
            
        ]);
    }
}
