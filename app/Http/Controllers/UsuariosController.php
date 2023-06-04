<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Notifications\UsuarioBaneado;
use App\Notifications\UsuarioActivado;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use App\Notifications\UsuarioCreadoAdmVerificar;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\DeletesUsers;


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
            $usuarioBaneo = $usuario->banned_at;
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
                'roles' => $roles,
                'estado' => $usuarioBaneo
            ];
        }

        // Filtrar por el filtro ingresado
        if ($filtro) {
            $detalle = array_filter($detalle, function ($item) use ($filtro) {
                $userMatches = str_contains($item['user_name'], $filtro);
                $roleMatches = str_contains($item['roles'], $filtro); // Agregar esta línea para filtrar por el campo "role"

                return $userMatches || $roleMatches; // Devolver true si alguna de las condiciones se cumple
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
        $request->validate([
            'roles' => 'required|array|min:1', // Validar que 'roles' sea requerido, un array y tenga al menos un elemento
        ]);
        // Obtener el rol del ID enviado
        $usuario = User::findOrFail($request->id);
        // Obtener los IDs de los permisos seleccionados
        $selectedRoles =  $request->roles;

        //Verificar que el array de permisos no este vacio
        if (!empty($selectedRoles)) {
            $usuario->syncRoles($selectedRoles); // Asignar o remover permiso
        }
    }    

    // La operación de suspender al usuario
    public function SuspenderUsuario(Request $request){
        $usuario = User::find($request->id);
        $usuario->ban();
        $usuario->notify(new UsuarioBaneado());
        return $usuario;
    }

    // La operación de activar al usuario
    public function ActivarUsuario(Request $request){
        $usuario = User::find($request->id);
        $usuario->unban();
        $usuario->notify(new UsuarioActivado());
        return $usuario;
    }
    // 
    public function CrearUsuario(Request $request){
        // Comprobando que los campos se hayan ingresado correctamente
        $this->validacion($request);
        
        $input = $request->all();
        $user = User::create([
            'name' => $input['user_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Cuando se crea un nuevo usuario se le debe dar el rol de Visitante como predeterminado, esto lo puedo cambair después
        $role = Role::findByName('Visitante');
        $user->assignRole($role);
        $user->notify(new UsuarioCreadoAdmVerificar());
        $user->sendEmailVerificationNotification();
        //$token = Password::getRepository()->create($user);

        //$user->sendPasswordResetNotification($token);
        return $user;
    }

    // La operación de Update CR[U]D
    public function ActualizarUsuario(Request $request)
    {
        $user = User::find($request->id);

        $this->validate($request, [
            'user_name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'same:confirmarContraseña|min:8', // Nueva regla para la contraseña
        ]);
    
        $input = $request->all();
    
        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['user_name'],
                'email' => $input['email'],
            ]);

            if (!empty($input['password'])) {
                $user->password = Hash::make($input['password']);
            }
            $user->save();
        }
    }

    // La operación de Delete CR[U]D
    public function EliminarUsuario(Request $request)
    {
        $user = User::find($request->id);
        $user->tokens->each->delete();
        $user->delete();
    }



    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['user_name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
    /* METODOS INTERNOS con camelPascal */
    // Validacion de campos con Laravel
    private function validacion(Request $request)
    {
        // La de anexos va en su propio método porque solamente es necesario verificarlo si se sube un archivo.
        $request->validate([
            'user_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|same:confirmarContraseña',
        ]);
    }
}
