<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Cog\Contracts\Ban\Bannable as BannableInterface;
use Cog\Laravel\Ban\Traits\Bannable;


class HandleInertiaRequests extends Middleware
{
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            // comparte los permisos asignados al usuario
            'auth.user.permissions' => fn () => $request->user()
            ? $request->user()->getAllPermissions()->pluck('name')->toArray()
            : null,
            // comparte los roles asignados al usuario
            'auth.user.roles' => fn () => $request->user()
                ? $request->user()->getRoleNames()
                : null,
            // comparte el estado de la sesión del usuario
            'auth.user.loggedIn' => $request->user() ? 1 : null,

        ]);
    }
}