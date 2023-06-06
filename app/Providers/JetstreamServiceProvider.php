<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;

use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\UsuarioBaneado;


class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                if($user->banned_at){
                    throw new TooManyRequestsHttpException(429, 'Too many login attempts. Please try again later.');
                }
                $user->failed_login_attempts = 0;
                $user->save();
                return $user;
            } else {
                if ($user->email != 'sadmin@admin.com' && $user->failed_login_attempts >= 2) {
                    $user->ban();
                    throw new TooManyRequestsHttpException(429, 'Too many login attempts. Please try again later.');
                }
                if ($user) {
                    $currentDateTime = Carbon::now();
                    $user->date_failed_login_attempts = $currentDateTime;
                    $user->save();
        
                    $user->increment('failed_login_attempts');
                }
            }
    });
}

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
