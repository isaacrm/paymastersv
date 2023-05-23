<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Inertia\Inertia;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    // Error 403
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedException) {
            return Inertia::render('Errores/403')->toResponse($request)->setStatusCode(403);
        }
    
        return parent::render($request, $exception);
    }

}
