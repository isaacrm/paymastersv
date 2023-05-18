<?php

use App\Http\Controllers\AguinaldoController;
use App\Http\Controllers\RentaMensualController;
use App\Http\Controllers\TechoLaboralController;
use App\Http\Controllers\TipoDocumentoController;
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
//Consultar Departamentos
Route::get('/data_departamentos', [DepartamentosController::class, 'ConsultarDepartamentos']);

/* MUNICIPIOS */
//Ver
Route::get('/tabla_municipios', [MunicipiosController::class, 'TablaMunicipios']);
//Agregar
Route::post('/agregar_municipio',[MunicipiosController::class, 'AgregarMunicipios']);
//Actualizar
Route::post('/actualizar_municipio', [MunicipiosController::class, 'ActualizarMunicipios']);
//Eliminar
Route::post('/eliminar_municipio/{id}', [MunicipiosController::class, 'EliminarMunicipios']);
//Consultar Municipios
Route::get('/data_municipios/{id}', [MunicipiosController::class, 'ConsultarMunicipios']);
//Consultar municipio para editar
Route::get('/municipio/{id}', [MunicipiosController::class, 'GetMunicipio']);

/* DIRECCIONES */
//Ver
Route::get('/tabla_direcciones', [DireccionesController::class, 'TablaDirecciones']);
//Agregar
Route::post('/agregar_direccion', [DireccionesController::class, 'AgregarDirecciones']);
//Actualizar
Route::post('/actualizar_direccion', [DireccionesController::class, 'ActualizarDirecciones']);
//Eliminar
Route::post('/eliminar_direccion/{id}', [DireccionesController::class, 'EliminarDirecciones']);
