<?php

namespace App\Http\Controllers;

use App\Models\Impresora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImpresoraController extends Controller
{
    /**
     * Muestra todas las Impresora.
     */
    public function index($cliente_id)
    {
        $impresoras = Impresora::getImpresorasByClienteId($cliente_id);
        return response()->json($impresoras, 200);  
    }

    /**
     * Guarda una nueva impresora.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'impresora_ip' => 'required|string',
            'estado' => 'required|integer',
            'zona' => 'nullable|string|max:255',
            'es_integrada' => 'nullable|boolean',
            'descripcion' => 'nullable|string',
            'cliente_id' => 'required|integer',
        ]);

        $impresora = Impresora::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Dataphone created successfully.',
            'data' => $impresora,
        ], 201); // Código HTTP 201: Creado
    }

    /**
     * Muestra una impresora específica.
     */
    public function show($id)
    {
        $impresora = Impresora::find($id);

        if (!$impresora) {
            return response()->json(['message' => 'Impresora no encontrada'], 404);
        }

        return response()->json($impresora, 200);
    }

    /**
     * Actualiza una impresora específica.
     */
    public function update(Request $request, $id)
    {
        $impresora = Impresora::find($id);

        if (!$impresora) {
            return response()->json(['message' => 'Impresora no encontrada'], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'impresora_ip' => 'sometimes|required|ip',
            'estado' => 'sometimes|required|boolean',
            'descripcion' => 'nullable|string',
            'cliente_id' => 'sometimes|required|integer',
        ]);

        $impresora->update($validatedData);

        return response()->json($impresora, 200);
    }

    /**
     * Elimina una impresora específica.
     */
    public function destroy($id)
    {
        $impresora = Impresora::find($id);

        if (!$impresora) {
            return response()->json(['message' => 'Impresora no encontrada'], 404);
        }

        $impresora->delete();

        return response()->json(['message' => 'Impresora eliminada correctamente'], 200);
    }

    /**
     * Obtiene las impresoras asociadas a un cliente específico.
     */
    public function getByClienteId($cliente_id)
    {
        if (!is_numeric($cliente_id)) {
            return response()->json(['message' => 'ID de cliente inválido'], 400);
        }

        $impresoras = Impresora::getImpresorasByClienteId($cliente_id);

        return response()->json($impresoras, 200);
    }
}

