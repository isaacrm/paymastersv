<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;

class PermisosController extends Controller
{
    public function TablaPermisos(Request $request)
    {
        // Para la paginación desde el servidor
    $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending;
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        
        $query = Permission::where('name','like','%' . $filtro . '%')
                ->orderBy($ordenarPor, $descendente ? 'desc' : 'asc');

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
    public function AgregarPermisos(Request $request)
    {
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        // Buscando si el permiso ya existe
        $permisoExistente = Permission::where('name', $request->name)->first();
        if ($permisoExistente) {
            return response()->json('error', 409);
        }

        // Buscando el rol Usuario
        $rolUser = Role::where('name', 'Visitante')->first(); // Obtén el rol correspondiente

        // Estableciendo el modelo donde se guardara la informacion
        $permiso = Permission::create([
            'name' => $request->name,
        ]);

        $permiso->syncRoles($rolUser); // Asigna el rol visitante al permiso nuevo
    }
    // La operación de Update CR[U]D
    public function ActualizarPermisos(Request $request)
    {
        $this->validacion($request);
        $permisos = Permission::find($request->id);
        $permisoExistente = Permission::where('name', $request->name)->first();
        if ($permisoExistente) {
            return response()->json('error', 409);
        }
        $permisos->name = $request->name;
        $permisos->save();
    }
    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarPermisos(Request $request)
    {
        $permisos = Permission::find($request->id);
        $permisos->delete();
    }
    /* METODOS INTERNOS con camelPascal */
    // Validacion de campos con Laravel
    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'name' => 'required|min:3',
        ]);
    }

    //Consulta a permisos
    public function CargarPermisos()
    {
        $permisos = Permission::all();
        return response()->json($permisos);
    }
}
