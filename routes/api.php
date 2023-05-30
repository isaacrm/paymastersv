<?php

use App\Http\Controllers\AguinaldoController;
use App\Http\Controllers\RentaMensualController;
use App\Http\Controllers\TechoLaboralController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\DepartamentosController;
use App\Http\Controllers\MunicipiosController;
use App\Http\Controllers\DireccionesController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\DescuentosController;
use App\Http\Controllers\EmpresasController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\CentroDeCostosController;
use App\Http\Controllers\EstadosCivilesController;
use App\Http\Controllers\GenerosController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\OcupacionesController;
use App\Http\Controllers\PlanillasController;
use App\Http\Controllers\RegistroController;
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

// ! Y este eliminar porque esta aqui?
Route::post('/eliminar/{id}', [TipoDocumentoController::class, 'EliminarTipoDocumentos']);


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
Route::post('/agregar_municipio', [MunicipiosController::class, 'AgregarMunicipios']);
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

/* DESCUENTOS */
// Ver
Route::get('/tabla_empresas', [EmpresasController::class, 'TablaEmpresas']);
// Agregar
Route::post('/agregar_empresa', [EmpresasController::class, 'AgregarEmpresas']);
// Actualiza
Route::post('/actualizar_empresa', [EmpresasController::class, 'ActualizarEmpresas']);
// Eliminar
Route::post('/eliminar_empresa/{id}', [EmpresasController::class, 'EliminarEmpresas']);
/* Puestos */
// Ver
Route::get('/puestos', [PuestoController::class, 'index']);
// Agregar
Route::post('/puestos_agregar', [PuestoController::class, 'store']);
// Actualizar
Route::post('/puestos_actualizar', [PuestoController::class, 'update']);
// Eliminar
Route::post('/puestos_eliminar/{id}', [PuestoController::class, 'destroy']);
//Consultar superior
Route::get('/puestos_consultar_superiores', [PuestoController::class, 'consultarSuperiores']);
//Consultar puestos
Route::get('/puestos_consultar_puestos', [PuestoController::class, 'consultarPuestos']);

/* Unidades */
// Ver
Route::get('/unidades', [UnidadesController::class, 'index']);
// Agregar
Route::post('/unidades_agregar', [UnidadesController::class, 'store']);
// Actualizar
Route::post('/unidades_actualizar', [UnidadesController::class, 'update']);
// Eliminar
Route::post('/unidades_eliminar/{id}', [UnidadesController::class, 'destroy']);
//Consultar nivel organizacional
Route::get('/unidades_consultar_unidades', [UnidadesController::class, 'consultarUnidades']);

/* Centro_de_costos */
// Ver
Route::get('/centro_de_costos', [CentroDeCostosController::class, 'index']);
// Agregar
Route::post('/centro_de_costos_agregar', [CentroDeCostosController::class, 'store']);
// Actualizar
Route::post('/centro_de_costos_actualizar', [CentroDeCostosController::class, 'update']);
// Eliminar
Route::post('/centro_de_costos_eliminar/{id}', [CentroDeCostosController::class, 'destroy']);
//Obtener los centro de costos
Route::get('/centro_de_costos_consultar_centro_de_costos', [CentroDeCostosController::class, 'centro_de_costos']);
//Obtener nombre
Route::post('/centro_de_costos/obtener_nombre', [CentroDeCostosController::class, 'obtenerNombre']);

/* Planillas */
// Ver
Route::get('/planillas', [PlanillasController::class, 'index']);
// Agregar
Route::post('/planillas_agregar', [PlanillasController::class, 'store']);
// Actualizar
Route::post('/planillas_actualizar', [PlanillasController::class, 'update']);
// Eliminar
Route::post('/planillas_eliminar/{id}', [PlanillasController::class, 'destroy']);
// Consultar existencia de Empleados y Descuentos. Ingresos extra no porque no son obligatorios.
Route::get('/planillas/comprobacion', [PlanillasController::class, 'CantidadRegistros']);
// Guardar ID seleccionado
Route::post('/planillas/redireccion', [PlanillasController::class, 'Redireccion']);

/* Generos */
Route::get('/generos', [GenerosController::class, 'index']);
Route::post('/generos_agregar', [GenerosController::class, 'store']);
Route::post('/generos_actualizar', [GenerosController::class, 'update']);
Route::post('/generos_eliminar/{id}', [GenerosController::class, 'destroy']);

/* Ocupaciones */
Route::get('/ocupaciones', [OcupacionesController::class, 'index']);
Route::post('/ocupaciones_agregar', [OcupacionesController::class, 'store']);
Route::post('/ocupaciones_actualizar', [OcupacionesController::class, 'update']);
Route::post('/ocupaciones_eliminar/{id}', [OcupacionesController::class, 'destroy']);

/* Estados Civiles */
Route::get('/estados_civiles', [EstadosCivilesController::class, 'index']);
Route::post('/estados_civiles_agregar', [EstadosCivilesController::class, 'store']);
Route::post('/estados_civiles_actualizar', [EstadosCivilesController::class, 'update']);
Route::post('/estados_civiles_eliminar/{id}', [EstadosCivilesController::class, 'destroy']);

/* Movimientos */
Route::get('/movimientos', [MovimientosController::class, 'index']);
Route::post('/movimientos_agregar', [MovimientosController::class, 'store']);
Route::post('/movimientos_actualizar', [MovimientosController::class, 'update']);
Route::post('/movimientos_eliminar/{id}', [MovimientosController::class, 'destroy']);

/* Detalle de Planillas / Registros */
Route::get('/registros', [RegistroController::class, 'index']);
//Route::post('/registros/agregar', [RegistroController::class, 'store']);
//Route::post('/registros/actualizar', [RegistroController::class, 'update']);
//Route::post('/registros/eliminar/{id}', [RegistroController::class, 'destroy']);
