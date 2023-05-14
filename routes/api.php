<?php

use App\Http\Controllers\TipoDocumentosController;
use App\Http\Controllers\DepartamentosController;
use App\Http\Controllers\MunicipiosController;
use App\Http\Controllers\DireccionesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* CUSTOM */
// Lo que esta en comillas, lo primero es la ruta que se llama en Vue, lo segundo el nombre de la funcion dentro del controlador indicado

/* TIPO DE DOCUMENTOS */
// Ver
Route::get('/tabla_tipo_documentos', [TipoDocumentosController::class, 'TablaTipoDocumentos']);
// Agregar
Route::post('/agregar', [TipoDocumentosController::class, 'AgregarTipoDocumentos']);
// Actualizar
Route::post('/actualizar', [TipoDocumentosController::class, 'ActualizarTipoDocumentos']);
// Eliminar
Route::post('/eliminar/{id}', [TipoDocumentosController::class, 'EliminarTipoDocumentos']);


/* DEPARTAMENTOS */
//Ver
Route::get('/tabla_departamentos', [DepartamentosController::class, 'TablaDepartamentos']);
//Agregar
Route::post('/agregar_departamento', [DepartamentosController::class, 'AgregarDepartamentos']);
//Actualizar
Route::post('/actualizar_departamento', [DepartamentosController::class, 'ActualizarDepartamentos']);
//Eliminar
Route::post('/eliminar_departamento/{id}', [DepartamentosController::class, 'EliminarDepartamentos']);

/* MUNICIPIOS */
//Ver
Route::get('/tabla_municipios', [MunicipiosController::class, 'TablaMunicipios']);
//Agregar
Route::post('/agregar_municipio',[MunicipiosController::class, 'AgregarMunicipios']);
//Actualizar
Route::post('/actualizar_municipio', [MunicipiosController::class, 'ActualizarMunicipios']);
//Eliminar
Route::post('/eliminar_municipio/{id}', [MunicipiosController::class, 'EliminarMunicipios']);

/* DIRECCIONES */
//Ver
Route::get('/tabla_direcciones', [DireccionesController::class, 'TablaDirecciones']);
//Agregar
Route::post('/agregar_direccion', [DireccionesController::class, 'AgregarDirecciones']);
//Actualizar
Route::post('/actualizar_direccion', [DireccionesController::class, 'ActualizarDirecciones']);
//Eliminar
Route::post('/eliminar_direccion/{id}', [DireccionesController::class, 'EliminarDirecciones']);