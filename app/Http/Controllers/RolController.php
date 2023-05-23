<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RolController extends Controller
{
    public function TablaRoles(Request $request)
    {
        // Para la paginación desde el servidor
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending;
        // Almacenando la consulta en una variable. Se almacena mas o menos algo asi $detalle = [ [], [], [] ]
        $query = Role::where('name','like','%' . $filtro . '%')
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

    //Tabla para mostrar los roles y sus permisos asignados    
    public function TablaRolesPermisos(Request $request)
    {

        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;

        $roles = Role::with('permissions:id,name')->get();

        $detalle = [];

        foreach ($roles as $rol) {
            $rolID = $rol->id;
            $rolName = $rol->name;
            $permisos = $rol->permissions->map(function ($permiso) {
                return [
                    'id' => $permiso->id,
                    'name' => $permiso->name
                ];
            });

            $detalle[] = [
                'id' => $rolID,
                'role_name' => $rolName,
                'permissions' => $permisos
            ];
        }

        // Filtrar por el filtro ingresado
        if ($filtro) {
            $detalle = array_filter($detalle, function ($item) use ($filtro) {
                return str_contains($item['role_name'], $filtro);
            });
          }

        $tuplas = count($detalle);

        // Paginación
        $inicio = ($pagina - 1) * $filasPorPagina;
        $detallePaginado = array_slice($detalle, $inicio, $filasPorPagina);

        // Información pertinente a la paginación para llamarlos en la vista
        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro
        ];

        // El json que se manda a la vista para poder visualizar la información
        return response()->json([
            'detalle' => $detallePaginado,
            'paginacion' => $paginacion,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

// La operación de Asignación de Permisos al Rol
    public function asignarPermisos(Request $request)
    {
        // Obtener el rol del ID enviado
        $role = Role::findOrFail($request->id);
        // Obtener los IDs de los permisos seleccionados
        $selectedPermissions =  $request->permissions;

        //Verificar que el array de permisos no este vacio
        if (!empty($selectedPermissions)) {
            $role->syncPermissions($selectedPermissions); // Asignar o remover permiso
        }
    
    }

// La operación de Create [C]RUD
    public function AgregarRoles(Request $request)
    {
        $this->validacion($request);
        // Determinando el nombre del rol
        $rolNombre = $request->name;
        // Buscando si el rol ya existe
        $rolExistente = Role::where('name', $rolNombre)->first();
        if ($rolExistente) {
            return response()->json('error', 409);
        }

        // Buscando el permiso Visualizar
        $permisoVisualizar = Permission::where('name', 'Inicio')->first(); // Obtén el rol correspondiente

        // Estableciendo el modelo donde se guardara la informacion
        $rol = Role::create([
            'name' => $request->name,
        ]);

        $rol->syncPermissions($permisoVisualizar); // Asigna el permiso visualizar  al rol nuevo
    }
    
    // La operación de Update CR[U]D
    public function ActualizarRoles(Request $request)
    {
        $this->validacion($request);
        $roles = Role::find($request->id);
        $rolExistente = Role::where('name', $request->name)->first();
        if ($rolExistente) {
            return response()->json('error', 409);
        }
        $roles->name = $request->name;
        $roles->save();
    }
    // La operación de Delete CRU[D]. En estas tablas pequeñas se eliminara todo, en las importantes sólo se cambiará de estado a false
    public function EliminarRoles(Request $request)
    {
        $roles = Role::find($request->id);
        $roles->delete();
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
    
    //Consulta a role
    public function CargarRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
