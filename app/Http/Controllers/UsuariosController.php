<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;


use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function TablaUsuarios(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;

        $usuarios = User::with('roles:id,name')->get();

        $detalle = [];

        foreach ($usuarios as $usuario) {
            $usuarioID = $usuario->id;
            $usuarioName = $usuario->name;
            $usuarioCorreo = $usuario->email;
            $roles = $usuario->roles->map(function ($rol) {
                return [
                    'id' => $rol->id,
                    'name' => $rol->name,
                ];
            });

            $detalle[] = [
                'id' => $usuarioID,
                'user_name' => $usuarioName,
                'email' => $usuarioCorreo,
                'roles' => $roles
            ];
        }

        // Filtrar por el filtro ingresado
        if ($filtro) {
            $detalle = array_filter($detalle, function ($item) use ($filtro) {
                return str_contains($item['user_name'], $filtro);
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
    public function asignarRoles(Request $request)
    {
        // Obtener el rol del ID enviado
        $usuario = User::findOrFail($request->id);
        // Obtener los IDs de los permisos seleccionados
        $selectedRoles =  $request->roles;

        //Verificar que el array de permisos no este vacio
        if (!empty($selectedRoles)) {
            $usuario->syncRoles($selectedRoles); // Asignar o remover permiso
        }
    
    }

    
}
