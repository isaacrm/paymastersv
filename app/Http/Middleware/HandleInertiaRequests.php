<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            // Lazily...
            'auth.user.permissions' => fn () => $request->user()
            ? $request->user()->getAllPermissions()->pluck('name')->toArray()
            : null,
            'auth.user.roles' => fn () => $request->user()
                ? $request->user()->getRoleNames()
                : null,
        ]);
    }
}