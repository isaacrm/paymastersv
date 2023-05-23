<?php

use App\Http\Controllers\TipoDocumentosController;
use App\Http\Controllers\GenerosController;
use App\Http\Controllers\OcupacionesController;
use App\Http\Controllers\EstadosCivilesController;
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

/*GENEROS*/
// Ver
Route::get('/tabla_generos', [GenerosController::class, 'TablaGeneros']);
// Agregar
Route::post('/agregarGeneros', [GenerosController::class, 'AgregarGeneros']);
// Actualizar
Route::post('/actualizarGeneros', [GenerosController::class, 'ActualizarGeneros']);
// Eliminar
Route::post('/eliminarGeneros/{id}', [GenerosController::class, 'EliminarGeneros']);


/* OCUPACIONES*/
// Ver
Route::get('/tabla_ocupaciones', [OcupacionesController::class, 'TablaOcupaciones']);
// Agregar
Route::post('/agregarOcupaciones', [OcupacionesController::class, 'AgregarOcupaciones']);
// Actualizar
Route::post('/actualizarOcupaciones', [OcupacionesController::class, 'ActualizarOcupaciones']);
// Eliminar
Route::post('/eliminarOcupaciones/{id}', [OcupacionesController::class, 'EliminarOcupaciones']);

/*ESTADOS CIVILES*/
// Ver
Route::get('/tabla_estados_civiles', [EstadosCivilesController::class, 'TablaEstadosCiviles']);
// Agregar
Route::post('/agregarEstado', [EstadosCivilesController::class, 'AgregarEstadosCiviles']);
// Actualizar
Route::post('/actualizarEstado', [EstadosCivilesController::class, 'ActualizarEstadosCiviles']);
// Eliminar
Route::post('/eliminarEstado/{id}', [EstadosCivilesController::class, 'EliminarEstadosCiviles']);