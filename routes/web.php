<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;



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
    RoleMiddleware::class . ':' . 'Administrador|SuperAdministrador'
    ])->group(function(){
        Route::get('/asignacion', function (){
            return Inertia::render('Administracion/AsignarPermisos');
        })->name('asignacion');

        Route::get('/roles', function (){
            return Inertia::render('Administracion/Roles');
        })->name('roles');

        Route::get('/permisos', function (){
            return Inertia::render('Administracion/Permisos');
        })->name('permisos');

        Route::get('/usuarios', function(){
            return Inertia::render('Administracion/Usuarios');
        })->name('usuarios');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleOrPermissionMiddleware::class . ':' . 'Administrador|SuperAdministrador|Contador|Visitante|Asistente|Usuario|Empleado|Planillero|Inicio'

    ])->group(function(){
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleOrPermissionMiddleware::class . ':' . 'Administrador|SuperAdministrador|Contador|Visitante|Asistente|Usuario|Empleado|Planillero'

])->group(function () {
    Route::get('/tipo_documentos', function () {
        return Inertia::render('Configuracion/TipoDocumentos');
    })->name('tipo_documentos');

    Route::get('/departamentos', function () {
        return Inertia::render('Direccion/Departamentos');
    })->name('departamentos');

    Route::get('/municipios', function () {
        return Inertia::render('Direccion/Municipios');
    })->name('municipios');

    Route::get('/direcciones', function () {
        return Inertia::render('Direccion/Direcciones');
    })->name('direcciones');

    Route::get('/renta_mensual', function () {
        return Inertia::render('Configuracion/RentaMensual');
    })->name('renta_mensual');

    Route::get('/techo_laboral', function () {
        return Inertia::render('Configuracion/TechoLaboral');
    })->name('techo_laboral');

    Route::get('/aguinaldo', function () {
        return Inertia::render('Configuracion/Aguinaldo');
    })->name('aguinaldo');

    Route::get('/ingresos', function () {
        return Inertia::render('Registros/Ingresos');
    })->name('ingresos');

    Route::get('/descuentos', function () {
        return Inertia::render('Registros/Descuentos');
    })->name('descuentos');

    Route::get('/empresas', function () {
        return Inertia::render('Registros/Empresas');
    })->name('empresas');

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