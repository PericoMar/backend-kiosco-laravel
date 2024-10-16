<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticuloController extends Controller
{
    public function getProductsWithCustomizations()
    {
        // Aqui asignamos el tipo de tarifa que queramos.
        $tipo_tarifa_venta = $tipo_tarifa_venta_Param ?? 2; 

        $products = DB::table('Articulos')
                        ->join('Tarifa_Venta', 'Articulos.id', '=', 'Tarifa_Venta.articulo_id')
                        ->select('Articulos.*', 'Tarifa_Venta.precio_venta as precio')
                        ->where('Tarifa_Venta.tipo_tarifa_id', '=', $tipo_tarifa_venta)
                        ->get();

        $questions = DB::table('Preguntas_Articulo')->get();

        $options = DB::table('Opciones_Preguntas_Articulo')->get();
        
        $productsWithCustomizations = $products->map(function($product) use ($questions, $options) {
            // Filtrar las preguntas que corresponden a este artículo
            $filteredQuestions = $questions->filter(function($question) use ($product) {
                return $question->articulo_id == $product->id;
            });
            // Preguntas y opciones correspondientes al artículo
            $customizationQuestions = $filteredQuestions->map(function($question) use ($options) {
                return [
                    'id' => $question->id,
                    'name' => $question->texto,
                    'questionType' => $question->tipo_pregunta ?? "single",
                    'maxChoices' => $question->unidades_maximas,
                    'minChoices' => $question->unidades_minimas,
                    'options' => $options->filter(function($option) use ($question) {
                        // Filtrar las opciones que pertenecen a la pregunta
                        return $option->pregunta_articulo_id == $question->id;
                    })->map(function($option) {
                        // Busca el producto asociado
                        return [
                            'id' => $option->id,
                            'value' => $option->suplemento ?? "Well done",
                            'img' => $product->imagen ?? null, // Si tiene una imagen, se asigna
                            'price' => $product->precio ?? 0.25 // Si tiene un precio, se asigna
                        ];
                    })->values()->toArray()
                ];
            });
            // Retornar el producto
            return [
                'id' => $product->id,
                'name' => $product->articulo,
                'price' => $product->precio,
                'taxes' => $product->impuestos ?? 0,
                'img' => $product->imagen ?? null,
                'familyId' => $product->familia_id,
                'description' => $product->descripcion,
                'customizationQuestions' => $customizationQuestions->values()->toArray()
            ];
        });

        // Devuelve los productos con sus customizaciones en formato JSON.
        return response()->json($productsWithCustomizations->toArray());
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
