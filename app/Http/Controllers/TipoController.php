<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tipo;
class TipoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
//ordenar desde el registro mas reciente
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $tipos = Tipo::orderBy('id', 'desc')->paginate(3);
        }
        else{
            $tipos = Tipo::where($criterio, 'like', '%'.$buscar. '%')->orderBy('id', 'desc')->paginate(3);
        }

        return [
            'pagination'        => [
                'total'         =>   $tipos->total(),
                'current_page'  =>   $tipos->currentPage(),
                'per_page'      =>   $tipos->perPage(),
                'last_page'     =>   $tipos->lastPage(),
                'from'          =>   $tipos->firstItem(),
                'to'            =>   $tipos->lastItem(),
            ],
            'tipos' => $tipos
        ];
    }
    public function selectTipo(Request $request){
        if(!$request ->ajax()) return redirect('/');
        $tipos = Tipo::where('condicion','=','1')
        ->select('id','nombre')
        ->orderBy('nombre','asc')->get();
        return ['tipos'=>$tipos];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tipo = new Tipo();
        $tipo->nombre = $request->nombre;
        $tipo->descripcion = $request->descripcion;
        $tipo->condicion = '1';
        $tipo->save();
    }


    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tipo = Tipo::findOrFail($request->id);
        $tipo->nombre = $request->nombre;
        $tipo->descripcion = $request->descripcion;
        $tipo->condicion = '1';
        $tipo->save();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tipo = Tipo::findOrFail($request->id);
        $tipo->condicion = '0';
        $tipo->save();
    }
    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tipo = Tipo::findOrFail($request->id);
        $tipo->condicion = '1';
        $tipo->save();
    }
}
