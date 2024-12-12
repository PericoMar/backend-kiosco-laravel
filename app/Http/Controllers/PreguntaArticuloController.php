<?php

namespace App\Http\Controllers;

use App\Models\PreguntaArticulo;
use Illuminate\Http\Request;

class PreguntaArticuloController extends Controller
{
    public function index($cliente_id)
    {
        $preguntas = PreguntaArticulo::where('cliente_id', $cliente_id)->get();
        // Yo quiero devolver la columna texto como name y el id solamente:
        $preguntas = $preguntas->map(function($pregunta) {
            return [
                'id' => $pregunta->id,
                'name' => $pregunta->texto
            ];
        });
        return response()->json($preguntas);
    }   

    public function store(Request $request, $cliente_id)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'productId' => 'required|integer',
                'questionType' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'status' => 'nullable|integer',
                'max' => 'nullable|integer',
                'min' => 'nullable|integer'
            ]);

            // Buscar la pregunta existente
            $pregunta = new PreguntaArticulo;

            // Actualizar los datos de la pregunta
            $pregunta->texto = $validatedData['name'];
            $pregunta->articulo_id = $validatedData['productId'];
            $pregunta->tipo_pregunta = 'multiple';
            $pregunta->descripcion = $validatedData['description'];
            $pregunta->estado = $validatedData['status'];
            $pregunta->unidades_maximas = $validatedData['max'];
            $pregunta->unidades_minimas = $validatedData['min'];
            $pregunta->cliente_id = $cliente_id;

            // Guardar cambios en la base de datos
            $pregunta->save();

            return response()->json(['message' => 'Pregunta actualizada exitosamente', 'articulo' => $pregunta], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['message' => 'Error de validación', 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return response()->json(['message' => 'Error al actualizar la pregunta', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'productId' => 'required|integer',
                'questionType' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'status' => 'nullable|integer',
                'max' => 'nullable|integer',
                'min' => 'nullable|integer'
            ]);

            // Buscar la pregunta existente
            $pregunta = PreguntaArticulo::findOrFail($id);

            // Actualizar los datos de la pregunta
            $pregunta->texto = $validatedData['name'];
            $pregunta->articulo_id = $validatedData['productId'];
            $pregunta->tipo_pregunta = $validatedData['questionType'];
            $pregunta->descripcion = $validatedData['description'];
            $pregunta->estado = $validatedData['status'];
            $pregunta->unidades_maximas = $validatedData['max'];
            $pregunta->unidades_minimas = $validatedData['min'];

            // Guardar cambios en la base de datos
            $pregunta->save();

            return response()->json(['message' => 'Pregunta actualizada exitosamente', 'pregunta' => $pregunta], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['message' => 'Error de validación', 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return response()->json(['message' => 'Error al actualizar la pregunta', 'error' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $pregunta = PreguntaArticulo::findOrFail($id);
        return response()->json($pregunta);
    }

    public function destroy($id)
    {
        $pregunta = PreguntaArticulo::findOrFail($id);
        $pregunta->delete();
        return response()->json(null, 204);
    }
}