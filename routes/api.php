<?php

use App\Http\Controllers\TipoDocumentosController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\CentroDeCostosController;
use App\Http\Controllers\UnidadesController;
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


/* Puestos */
// Ver
Route::get('/puestos', [PuestoController::class, 'index']);
// Agregar
Route::post('/puestos_agregar', [PuestoController::class, 'store']);
// Actualizar
Route::post('/puestos_actualizar', [PuestoController::class, 'update']);
// Eliminar
Route::post('/puestos_eliminar/{id}', [PuestoController::class, 'destroy']);

/* Unidades */
// Ver
Route::get('/unidades', [UnidadesController::class, 'index']);
// Agregar
Route::post('/unidades_agregar', [UnidadesController::class, 'store']);
// Actualizar
Route::post('/unidades_actualizar', [UnidadesController::class, 'update']);
// Eliminar
Route::post('/unidades_eliminar/{id}', [UnidadesController::class, 'destroy']);
//Obtener los supeiores
Route::get('/unidades_superiores', [UnidadesController::class, 'superiores']);
//Consultar superior
Route::get('/unidades_consultar_superiores', [UnidadesController::class, 'consultarSuperiores']);
//Obtener los centro de costos
Route::get('/unidades_centro_de_costos', [UnidadesController::class, 'centro_de_costos']);

/* Centro_de_costos */
// Ver
Route::get('/centro_de_costos', [CentroDeCostosController::class, 'index']);
// Agregar
Route::post('/centro_de_costos_agregar', [CentroDeCostosController::class, 'store']);
// Actualizar
Route::post('/centro_de_costos_actualizar', [CentroDeCostosController::class, 'update']);
// Eliminar
Route::post('/centro_de_costos_eliminar/{id}', [CentroDeCostosController::class, 'destroy']);