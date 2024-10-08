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

<<<<<<< HEAD
=======
// Ruta del dashboard
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    PermissionMiddleware::class . ':' . 'dashboard'
    ])->group(function(){
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
});

// Rusa de Roles, permisos, asignacion de roles, asignacion de permisos a roles
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ])->group(function(){
        Route::get('/asignacion', function (){
            return Inertia::render('Administracion/AsignarPermisos');
        })->name('asignacion')->middleware(RoleMiddleware::class . ':' . 'SuperAdministrador');

        Route::get('/roles', function (){
            return Inertia::render('Administracion/Roles');
        })->name('roles')->middleware(RoleMiddleware::class . ':' . 'SuperAdministrador');

        Route::get('/permisos', function (){
            return Inertia::render('Administracion/Permisos');
        })->name('permisos')->middleware(RoleMiddleware::class . ':' . 'SuperAdministrador');

        Route::get('/roles_estados', function(){
            return Inertia::render('Administracion/RolesYEstados');
        })->name('roles_estados')->middleware(RoleMiddleware::class . ':' . 'Administrador|SuperAdministrador');

        Route::get('/usuarios', function(){
            return Inertia::render('Administracion/Usuarios');
        })->name('usuarios')->middleware(RoleMiddleware::class . ':' . 'Administrador|SuperAdministrador');

        Route::get('/bitacora', function(){
            return Inertia::render('Bitacora/Bitacora');
        })->name('bitacora')->middleware(RoleMiddleware::class . ':' . 'Administrador|SuperAdministrador');

        Route::get('/bitacora_general', function(){
            return Inertia::render('Bitacora/BitacoraGeneral');
        })->name('bitacora_general')->middleware(RoleMiddleware::class . ':' . 'Administrador|SuperAdministrador');
});

// Rutas de registro
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
<<<<<<< HEAD
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
=======

    // * Contenedor de rutas
    $rutas = [
        'ingresos' => [
            'ruta' => '/ingresos',
            'render' => 'Registros/Ingresos',
            'nombre' => 'ingresos',
            'permiso' => 'registro.movimientos'
        ],
        'descuentos' => [
            'ruta' => '/descuentos',
            'render' => 'Registros/Descuentos',
            'nombre' => 'descuentos',
            'permiso' => 'registro.movimientos'
        ],
        'empresas' => [
            'ruta' => '/empresas',
            'render' => 'Registros/Empresas',
            'nombre' => 'empresas',
            'permiso' => 'registro.empresa'
        ],
        'puesto' => [
            'ruta' => '/puestos',
            'render' => 'Puestos/Puesto',
            'nombre' => 'puesto',
            'permiso' => 'registro.empresa'
        ],
        'unidad' => [
            'ruta' => '/unidades',
            'render' => 'Unidades/Unidades',
            'nombre' => 'unidades',
            'permiso' => 'registro.empresa'
        ],
        'CentroDeCostos' => [
            'ruta' => '/centro_de_costos',
            'render' => 'CentroDeCostos/CentroDeCostos',
            'nombre' => 'centro_de_costos',
            'permiso' => 'registro.empresa'
        ],
        'Movimientos'=>[
            'ruta' => '/movimientos',
            'render' => 'Movimientos/Movimientos',
            'nombre' => 'movimientos',
            'permiso' => 'registro.empresa'
        ],
        'Planillas'=>[
            'ruta' => '/planillas',
            'render' => 'Planillas/Planillas',
            'nombre' => 'planillas',
            'permiso' => 'registro.empresa'
        ],
        'Generos'=>[
            'ruta' => '/generos',
            'render' => 'Generos/Generos',
            'nombre' => 'generos',
            'permiso' => 'empleados.config'
        ],
        'Ocupaciones'=>[
            'ruta' => '/ocupaciones',
            'render' => 'Ocupaciones/Ocupaciones',
            'nombre' => 'ocupaciones',
            'permiso' => 'empleados.config'
        ],
        'Estados Civiles'=>[
            'ruta' => '/estados_civiles',
            'render' => 'Estados_civiles/Estados_civiles',
            'nombre' => 'estados_civiles',
            'permiso' => 'empleados.config'
        ],
        'Empleados'=>[
            'ruta' => '/empleados',
            'render' => 'Empleados/Empleados',
            'nombre' => 'Empleados',
            'permiso' => 'empleados.datos'
        ],
        'departamentos' => [
            'ruta' => '/departamentos',
            'render' => 'Direccion/Departamentos',
            'nombre' => 'departamentos',
            'permiso' => 'direccion'
        ],
        'municipios' => [
            'ruta' => '/municipios',
            'render' => 'Direccion/Municipios',
            'nombre' => 'municipios',
            'permiso' => 'direccion'
        ],
        'direcciones' => [
            'ruta' => '/direcciones',
            'render' => 'Direccion/Direcciones',
            'nombre' => 'direcciones',
            'permiso' => 'direccion'
        ],
        'tipo_documentos' => [
            'ruta' => '/tipo_documentos',
            'render' => 'Configuracion/TipoDocumentos',
            'nombre' => 'tipo_documentos',
            'permiso' => 'configuracion.doc'
        ],
        'renta_mensual' => [
            'ruta' => '/renta_mensual',
            'render' => 'Configuracion/RentaMensual',
            'nombre' => 'renta_mensual',
            'permiso' => 'configuracion.desc'
        ],
        'techo_laboral' => [
            'ruta' => '/techo_laboral',
            'render' => 'Configuracion/TechoLaboral',
            'nombre' => 'techo_laboral',
            'permiso' => 'configuracion.desc'
        ],
        'aguinaldo' => [
            'ruta' => '/aguinaldo',
            'render' => 'Configuracion/Aguinaldo',
            'nombre' => 'aguinaldo',
            'permiso' => 'configuracion.desc'
        ],
    ];
    foreach ($rutas as $ruta) {
        $middleware = isset($ruta['permiso']) ? (PermissionMiddleware::class . ':' . $ruta['permiso']) : null;

        Route::get($ruta['ruta'], function () use ($ruta) {
            return Inertia::render($ruta['render']);
        })->name($ruta['nombre'])->middleware($middleware);

        // Temporalmente asi, hay que adaptar lo de arriba para poderlo usar en forma de array tambien
        Route::get('/detalle_planillas/{idPlanilla}', function ($idPlanilla) {
            return Inertia::render('Planillas/DetallePlanillas', ['idPlanilla' => $idPlanilla]);
        })->name('detalle_planillas')->middleware($middleware);
    }
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8


});

