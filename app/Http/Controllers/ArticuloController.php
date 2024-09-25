<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticuloController extends Controller
{

    public function getProductsWithCustomizations()
    {
        // Obtener productos
        $products = DB::table('Articulos')->get();

        // Obtener preguntas de personalización
        $questions = DB::table('Preguntas_Articulo')->get();

        // Obtener opciones de personalización
        $options = DB::table('Opciones_Preguntas_Articulo')->get();

        // Mapear los datos
        $customizationQuestions = $questions->map(function($question) use ($options) {
            return [
                'id' => $question->id,
                'name' => $question->texto,
                'questionType' => $question->tipo_pregunta,
                'maxChoices' => $question->unidades_maximas,
                'options' => $options->filter(function($option) use ($question) {
                    return $option->pregunta_articulo_id == $question->id;
                })->map(function($option) {
                    return [
                        'id' => $option->id,
                        'value' => $option->suplemento,
                        'img' => null, // Si tienes imágenes, debes añadir el campo de imagen
                        'price' => null // Si tienes precios, debes añadir el campo de precio
                    ];
                })->toArray()
            ];
        });

        $productsWithCustomizations = $products->map(function($product) use ($customizationQuestions) {
            return [
                'id' => $product->id,
                'name' => $product->articulo,
                'price' => $product->precio, // Asegúrate de que el nombre del campo coincida
                'taxes' => $product->tipo_iva_id, // Puedes mapear esto según tus necesidades
                'img' => $product->imagen,
                'familyId' => $product->familia_id,
                'description' => $product->descripcion, // Asegúrate de que el nombre del campo coincida
                'customizations' => [], // Añade personalizaciones si es necesario
                'customizationQuestions' => $customizationQuestions
            ];
        });

        return response()->json($productsWithCustomizations);
    }
    public function index()
    {
        $articulos = Articulo::all();
        return response()->json($articulos);
    }

    public function store(Request $request)
    {
        $articulo = Articulo::create($request->all());
        return response()->json($articulo, 201);
    }

    public function show($id)
    {
        $articulo = Articulo::findOrFail($id);
        return response()->json($articulo);
    }

    public function update(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->update($request->all());
        return response()->json($articulo);
    }

    public function destroy($id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->delete();
        return response()->json(null, 204);
    }
}
