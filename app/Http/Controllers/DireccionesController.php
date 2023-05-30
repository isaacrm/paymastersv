<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Direccion;

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
        
        $query = Direccion::select('direccions.*', 'municipios.nombre AS nombre_municipio', 'departamentos.id AS departamento_id','departamentos.nombre AS departamento_nombre')
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
    }
    // La operación de Update CR[U]D
    public function ActualizarDirecciones(Request $request)
    {
        $this->validacion($request);
        $direcciones = Direccion::find($request->id);
        $direcciones->calle = $request->calle;
        $direcciones->colonia = $request->colonia;
        $direcciones->identificador_casa = $request->identificador_casa;
        $direcciones->apto_local = $request->apto_local;
        $direcciones->municipio_id = $request->municipio_id;
        $direcciones->save();
    }
     // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
     public function EliminarDirecciones(Request $request)
     {
         $direcciones = Direccion::find($request->id);
         $direcciones->delete();
     }

     public function consultar_id_nombre()
     {
         $datos = Direccion::select('id', 'calle as name')->get();
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
