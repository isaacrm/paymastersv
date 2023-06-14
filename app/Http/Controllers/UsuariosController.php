<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Notifications\UsuarioBaneado;
use App\Notifications\UsuarioActivado;
use App\Notifications\PasswordActualizadaAdm; 
use App\Notifications\CorreoPasswordActualizadaAdm; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use App\Notifications\UsuarioCreadoAdmVerificar;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Laravel\Fortify\Rules\Password as PasswordRules;



use Illuminate\Http\Request;

class UsuariosController extends Controller
{

    public function TablaUsuarios(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $ordenarPor = $request->sortBy;
        $descendente = $request->descending;

        $query = User::with('roles:id,name');

        // Filtrar por el filtro ingresado
        if ($filtro) {
            $query->where(function ($q) use ($filtro) {
                $q->where('name', 'like', '%' . $filtro . '%')
                    ->orWhere('email', 'like', '%' . $filtro . '%')
                    ->orWhere(function ($q) use ($filtro) {
                        if ($filtro === 'Pendiente') {
                            $q->whereNull('email_verified_at');
                        } elseif ($filtro === 'Verificado') {
                            $q->whereNotNull('email_verified_at');
                        }
                    })
                    ->orWhereHas('roles', function ($q) use ($filtro) {
                        $q->where('name', 'like', '%' . $filtro . '%');
                    });
            });
        }

        // Ordenar los usuarios si se especifica el ordenamiento
        if ($ordenarPor) {
            $query->orderBy($ordenarPor, $descendente ? 'desc' : 'asc');
        }

        // Obtener el total de usuarios antes de la paginación
        $totalUsuarios = $query->count();

        // Aplicar la paginación
        $usuariosPaginados = $query->paginate($filasPorPagina);

        // Transformar los usuarios en el formato deseado
        $detallePaginado = $usuariosPaginados->map(function ($usuario) {
            $verificacionEmail = $usuario->email_verified_at ? "Verificado" : "Pendiente";

            return [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'email_verified_at' => $verificacionEmail,
                'roles' => $usuario->roles->map(function ($rol) {
                    return [
                        'id' => $rol->id,
                        'name' => $rol->name,
                    ];
                }),
                'estado' => $usuario->banned_at
            ];
        });

        // Información pertinente a la paginación para pasar a la vista
        $paginacion = [
            'tuplas' => $totalUsuarios,
            'pagina' => $usuariosPaginados->currentPage(),
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro,
            'ordenarPor' => $ordenarPor
        ];

        // El JSON que se envía a la vista para mostrar la información
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

        if($usuario->failed_login_attempts == 2){
            $usuario->failed_login_attempts = 0;
            $usuario->save();
            $usuario->unban();
            $usuario->notify(new UsuarioActivado());
        }
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
            'name' => $input['name'],
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

        // Validar los datos
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], // el correo puede no ser único si es para ese usuario 
            'password' => 'same:confirmarContraseña|min:8', // Nueva regla para la contraseña, no pedimos que sea obligatoria mientras se edita
        ]);
    
        $input = $request->all(); // le agrega todos los campos evniados desde la vista
    
        if ($input['email'] !== $user->email && !empty($input['password'])) {
            // Notificar al usuario sobre el cambio de contraseña
            $newEmail = $input['email'];
            $user->email = $newEmail;
            $user->notify(new CorreoPasswordActualizadaAdm($input['password']));
            $user->password = Hash::make($input['password']); // Encriptar la nueva contraseña
    
            if ($user instanceof MustVerifyEmail) {
                // Actualizar el usuario y enviar el correo de verificación
                $this->updateVerifiedUser($user, $input);
            }
        } elseif ($input['email'] !== $user->email) {
            // Actualizar el correo y enviar la notificación de verificación de correo
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ]);
    
            if ($user instanceof MustVerifyEmail) {
                $this->updateVerifiedUser($user, $input);
            }
        } elseif (!empty($input['password'])) {
            // Notificar al usuario sobre el cambio de contraseña
            $user->notify(new PasswordActualizadaAdm($input['password']));
            $user->password = Hash::make($input['password']); // Encriptar la nueva contraseña
        } else {
            // Si no se cambia el correo ni la contraseña, simplemente actualizar el nombre
            $user->forceFill([
                'name' => $input['name'],
            ]);
        }
        $user->save();
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users|same:password',// el adm pone de contra el correo del usuario
            'password' => 'required|same:confirmarContraseña', //lo coloque así porque necesito que sea requerido e igual al campo de confirmar contraseña
        ]);
    }
}
