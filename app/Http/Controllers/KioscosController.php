<?php

namespace App\Http\Controllers;

use App\Models\Kiosco;
use Illuminate\Http\Request;

class KioscosController extends Controller
{
    public function getKioscoByNumSerie($numSerie) {
        $kiosco = Kiosco::where('num_serie', $numSerie)->first();

        if (!$kiosco) {
            return response()->json(['message' => 'No existe un kiosco con ese número de serie'], 404);
        }

        return response()->json($kiosco);
    }

    // Mostrar todos los kioscos
    public function index()
    {
        $kioscos = Kiosco::all();
        return response()->json($kioscos);
    }

    // Mostrar un kiosco específico
    public function show($id)
    {
        $kiosco = Kiosco::find($id);

        if (!$kiosco) {
            return response()->json(['message' => 'Kiosco no encontrado'], 404);
        }

        return response()->json($kiosco);
    }

    // Crear un nuevo kiosco
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'num_serie' => 'required|string|max:255',
            'datafono_id' => 'nullable|integer',
            'cliente_id' => 'nullable|integer',
            'nombre' => 'required|string|max:255',
        ]);

        $kiosco = Kiosco::create($validatedData);

        return response()->json(['message' => 'Kiosco creado con éxito', 'kiosco' => $kiosco], 201);
    }

    // Actualizar un kiosco existente
    public function update(Request $request, $id)
    {
        $kiosco = Kiosco::find($id);

        if (!$kiosco) {
            return response()->json(['message' => 'Kiosco no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'num_serie' => 'nullable|string|max:255',
            'datafono_id' => 'nullable|integer',
            'cliente_id' => 'nullable|integer',
            'nombre' => 'nullable|string|max:255',
        ]);

        $kiosco->update($validatedData);

        return response()->json(['message' => 'Kiosco actualizado con éxito', 'kiosco' => $kiosco]);
    }

    // Eliminar un kiosco
    public function destroy($id)
    {
        $kiosco = Kiosco::find($id);

        if (!$kiosco) {
            return response()->json(['message' => 'Kiosco no encontrado'], 404);
        }

        $kiosco->delete();

        return response()->json(['message' => 'Kiosco eliminado con éxito']);
    }
}
