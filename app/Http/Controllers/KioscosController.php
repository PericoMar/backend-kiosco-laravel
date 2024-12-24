<?php

namespace App\Http\Controllers;

use App\Models\Kiosco;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
    public function index($cliente_id)
    {
        $kioscos = Kiosco::getKioscosByClienteId($cliente_id);
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
            'num_serie' => 'required|string|max:255|unique:Kioscos,num_serie',
            'impresora_integrada_id' => 'nullable|integer|unique:Kioscos,impresora_integrada_id',
            'datafono_id' => 'nullable|integer',
            'cliente_id' => 'nullable|integer',
            'nombre' => 'required|string|max:255',
            'estado' => 'required|integer',
        ], [
            'num_serie.unique' => 'El número de serie ya está en uso.',
            'impresora_integrada_id.unique' => 'La impresora ya está asignada a otro kiosco.',
        ]);

        $validatedData['created_at'] = Carbon::now()->format('Y-m-d\TH:i:s');
        $validatedData['updated_at'] = Carbon::now()->format('Y-m-d\TH:i:s');

        $kiosco = Kiosco::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Kiosco creado con exito.',
            'data' => $kiosco,
        ], 201); // Código HTTP 201: Creado
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
