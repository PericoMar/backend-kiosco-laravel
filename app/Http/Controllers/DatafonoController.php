<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Datafono;

class DatafonoController extends Controller
{
    // Método para listar todos los datafonos
    public function index($cliente_id)
    {
        $datafonos = Datafono::getDatafonosByClienteId($cliente_id);
        return response()->json($datafonos);
    }

    // Método para mostrar un datafono específico
    public function show($id)
    {
        $datafono = Datafono::find($id);

        if (!$datafono) {
            return response()->json(['message' => 'Datafono no encontrado'], 404);
        }

        return response()->json($datafono);
    }

    // Método para crear un nuevo datafono
    public function store(Request $request)
    {
        // Validación de los datos entrantes
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'num_serie' => 'required|string|max:255|unique:Datafonos,num_serie',
            'TID' => 'required|string|max:255|unique:Datafonos,TID',
            'estado' => 'required|integer',
            'descripcion' => 'nullable|string',
            'zona' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
            'devoluciones' => 'nullable|boolean',
            'cliente_id' => 'required|integer|exists:clientes,id', // Asegura que cliente_id exista
        ], [
            'num_serie.unique' => 'El número de serie ya está en uso.',
            'TID.unique' => 'El TID ya está en uso.',
        ]);

        // Crear un nuevo dataphone
        $dataphone = Datafono::create($validatedData);

        // Retornar la respuesta
        return response()->json([
            'success' => true,
            'message' => 'Dataphone created successfully.',
            'data' => $dataphone,
        ], 201); // Código HTTP 201: Creado
    }

    // Método para actualizar un datafono
    public function update(Request $request, $id)
    {
        $dataphone = Datafono::find($id);

        if (!$dataphone) {
            return response()->json(['message' => 'Datafono no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'num_serie' => 'required|string|max:255',
            'TID' => 'nullable|string|max:50',
            'estado' => 'nullable|integer',
            'descripcion' => 'nullable|string|max:1000',
            'supervisor' => 'nullable|string|max:255',
            'devoluciones' => 'nullable|boolean',
        ]);

        $dataphone->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Datafono editado con exito.',
            'data' => $dataphone,
        ], 201); 
    }

    // Método para eliminar un datafono
    public function destroy($id)
    {
        $datafono = Datafono::find($id);

        if (!$datafono) {
            return response()->json(['message' => 'Datafono no encontrado'], 404);
        }

        $datafono->delete();

        return response()->json(['message' => 'Datafono eliminado correctamente']);
    }
}

