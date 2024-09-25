<?php

namespace App\Http\Controllers;

use App\Models\OpcionPreguntaArticulo;
use Illuminate\Http\Request;

class OpcionPreguntaArticuloController extends Controller
{
    public function index()
    {
        $opciones = OpcionPreguntaArticulo::all();
        return response()->json($opciones);
    }

    public function store(Request $request)
    {
        $opcion = OpcionPreguntaArticulo::create($request->all());
        return response()->json($opcion, 201);
    }

    public function show($id)
    {
        $opcion = OpcionPreguntaArticulo::findOrFail($id);
        return response()->json($opcion);
    }

    public function update(Request $request, $id)
    {
        $opcion = OpcionPreguntaArticulo::findOrFail($id);
        $opcion->update($request->all());
        return response()->json($opcion);
    }

    public function destroy($id)
    {
        $opcion = OpcionPreguntaArticulo::findOrFail($id);
        $opcion->delete();
        return response()->json(null, 204);
    }
}
