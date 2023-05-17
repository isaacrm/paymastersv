<?php

use App\Http\Controllers\AguinaldoController;
use App\Http\Controllers\RentaMensualController;
use App\Http\Controllers\TechoLaboralController;
use App\Http\Controllers\TipoDocumentoController;
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
Route::get('/tipo_documentos/tabla', [TipoDocumentoController::class, 'TablaTipoDocumentos']);
// Agregar
Route::post('/tipo_documentos/agregar', [TipoDocumentoController::class, 'AgregarTipoDocumentos']);
// Actualizar
Route::post('/tipo_documentos/actualizar', [TipoDocumentoController::class, 'ActualizarTipoDocumentos']);
// Eliminar
Route::post('/tipo_documentos/eliminar/{id}', [TipoDocumentoController::class, 'EliminarTipoDocumentos']);

/* RENTA MENSUAL */
// Ver
Route::get('/renta_mensual/tabla', [RentaMensualController::class, 'TablaRentaMensual']);
// Agregar
Route::post('/renta_mensual/agregar', [RentaMensualController::class, 'AgregarRentaMensual']);
// Actualizar
Route::post('/renta_mensual/actualizar', [RentaMensualController::class, 'ActualizarRentaMensual']);
// Eliminar
Route::post('/renta_mensual/eliminar/{id}', [RentaMensualController::class, 'EliminarRentaMensual']);

/* TECHO LABORAL */
// Ver
Route::get('/techo_laboral/tabla', [TechoLaboralController::class, 'TablaTechoLaboral']);
// Agregar
Route::post('/techo_laboral/agregar', [TechoLaboralController::class, 'AgregarTechoLaboral']);
// Actualizar
Route::post('/techo_laboral/actualizar', [TechoLaboralController::class, 'ActualizarTechoLaboral']);
// Eliminar
Route::post('/techo_laboral/eliminar/{id}', [TechoLaboralController::class, 'EliminarTechoLaboral']);

/* AGUINALDO */
// Ver
Route::get('/aguinaldo/tabla', [AguinaldoController::class, 'TablaAguinaldo']);
// Agregar
Route::post('/aguinaldo/agregar', [AguinaldoController::class, 'AgregarAguinaldo']);
// Actualizar
Route::post('/aguinaldo/actualizar', [AguinaldoController::class, 'ActualizarAguinaldo']);
// Eliminar
Route::post('/aguinaldo/eliminar/{id}', [AguinaldoController::class, 'EliminarAguinaldo']);
