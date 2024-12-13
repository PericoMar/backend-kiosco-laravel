<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClientesController extends Controller
{
    public function getSubscriptionStatus($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $status = $cliente->estado_suscripcion == 1 ? 'activo' : 'inactivo';

        return response()->json([
            'cliente_id' => $id,
            'subscription_status' => $status,
            'fecha_alta' => $cliente->fecha_alta,
            'fecha_baja' => $cliente->fecha_baja,
        ], 200);
    }

    /**
     * Listar todos los clientes.
     */
    public function index()
    {
        $clientes = Cliente::all();
        return response()->json($clientes, 200);
    }

    /**
     * Mostrar un cliente específico.
     */
    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'telefono' => 'nullable|string|max:15',
            'estado_suscripcion' => 'required|boolean',
            'fecha_alta' => 'required|date',
            'num_kioscos' => 'required|integer|min:0',
        ]);

        $cliente = Cliente::create($validated);

        return response()->json(['message' => 'Cliente creado con éxito', 'cliente' => $cliente], 201);
    }

    /**
     * Actualizar un cliente existente.
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:clientes,email,' . $id,
            'telefono' => 'nullable|string|max:15',
            'estado_suscripcion' => 'sometimes|required|boolean',
            'fecha_alta' => 'sometimes|required|date',
            'fecha_baja' => 'nullable|date',
            'num_kioscos' => 'sometimes|required|integer|min:0',
        ]);

        $cliente->update($validated);

        return response()->json(['message' => 'Cliente actualizado con éxito', 'cliente' => $cliente], 200);
    }

    /**
     * Eliminar un cliente.
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();

        return response()->json(['message' => 'Cliente eliminado con éxito'], 200);
    }
   
}
