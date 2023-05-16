<?php

use App\Http\Controllers\TipoDocumentosController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\DescuentosController;
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

/* INGRESOS */
// Ver
Route::get('/tabla_ingresos', [IngresosController::class, 'TablaIngresos']);
// Agregar
Route::post('/agregar_ingreso', [IngresosController::class, 'AgregarIngresos']);
// Actualiza
Route::post('/actualizar_ingreso', [IngresosController::class, 'ActualizarIngresos']);
// Eliminar
Route::post('/eliminar_registro/{id}', [IngresosController::class, 'EliminarIngresos']);

/* DESCUENTOS */
// Ver
Route::get('/tabla_descuentos', [DescuentosController::class, 'TablaDescuentos']);
// Agregar
Route::post('/agregar_descuento', [DescuentosController::class, 'AgregarDescuentos']);
// Actualiza
Route::post('/actualizar_descuento', [DescuentosController::class, 'ActualizarDescuentos']);
// Eliminar
Route::post('/eliminar_descuento/{id}', [DescuentosController::class, 'EliminarDescuentos']);