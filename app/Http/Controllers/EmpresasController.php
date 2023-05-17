<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresasController extends Controller
{
    public function TablaEmpresas(Request $request)
    {
        $pagina = $request->page;
        $filasPorPagina = $request->rowsPerPage;
        $filtro = $request->filter;
        $query = Empresa::where('nombre', 'like', '%' . $filtro . '%')->orderBy('id');
        $tuplas = $query->count();
        $detalle = $query->skip(($pagina - 1) * $filasPorPagina)
            ->take($filasPorPagina)
            ->get();
        $paginacion = [
            'tuplas' => $tuplas,
            'pagina' => $pagina,
            'filasPorPagina' => $filasPorPagina,
            'filtro' => $filtro
        ];
        return response()->json([
            'detalle' => $detalle,
            'paginacion' => $paginacion,
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    // La operación de Create [C]RUD
    public function AgregarEmpresas(Request $request)
    {
        $this->validacion($request);
        $empresas = new Empresa();
        $empresas->nombre = $request->nombre;
        $empresas->nit = $request->nit;
        $empresas->telefono = $request->telefono;
        $empresas->nrc = $request->nrc;
        $empresas->email = $request->email;
        $empresas->sitio_web = $request->sitio_web;
        $empresas->numero_patronal = $request->numero_patronal;
        $empresas->representante_legal = $request->representante_legal;
        $empresas->save();
    }
    // La operación de Update CR[U]D
    public function ActualizarEmpresas(Request $request)
    {
        $this->validacion($request);
        $empresas = Empresa::find($request->id);
        $empresas->nombre = $request->nombre;
        $empresas->nit = $request->nit;
        $empresas->telefono = $request->telefono;
        $empresas->nrc = $request->nrc;
        $empresas->email = $request->email;
        $empresas->sitio_web = $request->sitio_web;
        $empresas->numero_patronal = $request->numero_patronal;
        $empresas->representante_legal = $request->representante_legal;
        $empresas->save();
    }
    // La operación de Delete CR[U]D
    public function EliminarEmpresas(Request $request)
    {
        $empresas = Empresa::find($request->id);
        $empresas->delete();
    }
    // La operación de validación
    private function validacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:30',
            'nit' => ['required', 'regex:/^\d{4}-\d{6}-\d{3}-\d$/'],
            'telefono' => ['required', 'numeric', 'digits_between:1,8', 'regex:/^[267][0-9]*$/'],
            'nrc' => ['required', 'regex:/^\d{6}-\d$/'],
            'email' => 'required|email|max:75',
            'sitio_web' => 'required|max:250',
            'numero_patronal' => 'required|max:50',
            'representante_legal' => 'required|max:250',
        ]);
    }
}
