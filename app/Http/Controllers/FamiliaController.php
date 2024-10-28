<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

class FamiliaController extends Controller
{
    public function index(): JsonResponse
    {
        $families = Familia::getFamilies();
        return response()->json($families);
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'img' => 'required|string', // Suponiendo que es la ruta de la imagen
            ]);


            $familia = new Familia();
            $familia->codigo = $validatedData['name'];
            $familia->imagen = $validatedData['img'];
            $familia->estado = 0; // O lo que consideres necesario
            $familia->visible_TPV = true; // O false, según sea necesario

            // Guardar el nuevo artículo en la base de datos
            $familia->save();

            return response()->json(['message' => 'Producto creado exitosamente', 'articulo' => $familia], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['message' => 'Error de validación', 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return response()->json(['message' => 'Error al crear el producto', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $familia = Familia::findOrFail($id);
        return response()->json($familia);
    }

    public function update(Request $request, $id)
    {
        $familia = Familia::findOrFail($id);
        $familia->update($request->all());
        return response()->json($familia);
    }

    public function destroy($id)
    {
        $familia = Familia::findOrFail($id);
        $familia->delete();
        return response()->json(null, 204);
    }
}
