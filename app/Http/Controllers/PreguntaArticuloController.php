<?php

namespace App\Http\Controllers;

use App\Models\PreguntaArticulo;
use Illuminate\Http\Request;

class PreguntaArticuloController extends Controller
{
    public function index()
    {
        $preguntas = PreguntaArticulo::all();
        return response()->json($preguntas);
    }

    public function store(Request $request)
    {
        $pregunta = PreguntaArticulo::create($request->all());
        return response()->json($pregunta, 201);
    }

    public function show($id)
    {
        $pregunta = PreguntaArticulo::findOrFail($id);
        return response()->json($pregunta);
    }

    public function update(Request $request, $id)
    {
        $pregunta = PreguntaArticulo::findOrFail($id);
        $pregunta->update($request->all());
        return response()->json($pregunta);
    }

    public function destroy($id)
    {
        $pregunta = PreguntaArticulo::findOrFail($id);
        $pregunta->delete();
       
    }
}