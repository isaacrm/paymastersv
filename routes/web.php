<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/tipo_documentos', function () {
        return Inertia::render('Configuracion/TipoDocumentos');
    })->name('tipo_documentos');

    Route::get('/generos', function () {
        return Inertia::render('Configuracion/Generos');
    })->name('generos');

    Route::get('/ocupaciones', function () {
        return Inertia::render('Configuracion/Ocupaciones');
    })->name('ocupaciones');

    Route::get('/estados_civiles', function () {
        return Inertia::render('Configuracion/EstadosCiviles');
    })->name('estados_civiles');


});

