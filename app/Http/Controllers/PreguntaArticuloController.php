<?php

namespace App\Http\Controllers;

use App\Models\PreguntaArticulo;
use Illuminate\Http\Request;

class PreguntaArticuloController extends Controller
{
    public function index()
    {
        $preguntas = PreguntaArticulo::all();
        // Yo quiero devolver la columna texto como name y el id solamente:
        $preguntas = $preguntas->map(function($pregunta) {
            return [
                'id' => $pregunta->id,
                'name' => $pregunta->texto
            ];
        });
        return response()->json($preguntas);
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'productId' => 'required|integer',
                'max' => 'required|integer',
                'min' => 'required|integer'
            ]);

            // protected $fillable = [
            //     'orden',
            //     'texto',
            //     'articulo_id',
            //     'tipo_pregunta',
            //     'unidades_maximas',
            //     'unidades_minimas'
            // ];

            $pregunta = new PreguntaArticulo();
            $pregunta->texto = $validatedData['name'];
            $pregunta->articulo_id = $validatedData['productId'];
            $pregunta->unidades_maximas = $validatedData['max'];
            $pregunta->unidades_minimas = $validatedData['min'];

            // Guardar el nuevo artículo en la base de datos
            $pregunta->save();

            return response()->json(['message' => 'Producto creado exitosamente', 'articulo' => $pregunta], 201);
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