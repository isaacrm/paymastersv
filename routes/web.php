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


    // * Contenedor de rutas
    $rutas = [
        'puesto' => [
            'ruta' => '/puestos',
            'render' => 'Puestos/Puesto',
            'nombre' => 'puesto'
        ],
        'unidad' => [
            'ruta' => '/unidades',
            'render' => 'Unidades/Unidades',
            'nombre' => 'unidades'
        ],
        'CentroDeCostos' => [
            'ruta' => '/centro_de_costos',
            'render' => 'CentroDeCostos/CentroDeCostos',
            'nombre' => 'centro_de_costos'
        ],
    ];

    foreach ($rutas as $ruta) {
        Route::get($ruta['ruta'], function () use ($ruta) {
            return Inertia::render($ruta['render']);
        })->name($ruta['nombre']);
    }
});
