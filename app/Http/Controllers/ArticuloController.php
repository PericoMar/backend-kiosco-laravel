<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TarifaVenta;

class ArticuloController extends Controller
{
    public function getProductsWithCustomizations()
    {
        // Aqui asignamos el tipo de tarifa que queramos.
        $tipo_tarifa_venta = $tipo_tarifa_venta_Param ?? 1; 

        $products = DB::table('Articulos')
            ->join('Tarifa_Venta', 'Articulos.id', '=', 'Tarifa_Venta.articulo_id')
            ->select('Articulos.*', 'Tarifa_Venta.precio_venta as precio')
            ->where('Tarifa_Venta.tipo_tarifa_id', '=', $tipo_tarifa_venta)
            ->whereNotIn('Articulos.id', function($query) {
                $query->select('articulo_id')
                    ->from('Opciones_Preguntas_Articulo');
            })
            ->get();

        
        Log::info('Productos: ' . $products);

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
                        // Coger el producto con el id $option->id
                        $modifier = DB::table('Articulos')->where('id', $option->articulo_id)->first();
                        $tarifa_venta = DB::table('Tarifa_Venta')->where('articulo_id', $option->articulo_id)->first();
                        return [
                            'id' => $option->id,
                            'value' => $modifier->articulo,
                            'img' => $modifier->imagen ?? null, // Si tiene una imagen, se asigna
                            'price' => $tarifa_venta->precio_venta // Si tiene un precio, se asigna
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
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'img' => 'required|string', // Suponiendo que es la ruta de la imagen
                'familyId' => 'required|integer',
                'description' => 'nullable|string',
            ]);

            // Crear una nueva instancia del modelo Articulo
            $articulo = new Articulo();
            $articulo->articulo = $validatedData['name'];
            $articulo->familia_id = $validatedData['familyId'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->imagen = $validatedData['img'];
            $articulo->estado = 0; // O lo que consideres necesario
            $articulo->visible_TPV = true; // O false, según sea necesario
            $articulo->tipo_iva_id = 1; // Establece un valor por defecto o recibe desde el request

            // Guardar el nuevo artículo en la base de datos
            $articulo->save();

            // Guardar el precio en la tabla Tarifa_venta
            $tarifaVenta = new TarifaVenta();
            $tarifaVenta->precio_venta = $validatedData['price'];
            $tarifaVenta->tipo_tarifa_id = 1; // Establece el tipo tarifa como 1
            $tarifaVenta->articulo_id = $articulo->id; // Relaciona con el nuevo artículo

            // Guardar la tarifa en la base de datos
            $tarifaVenta->save();

            return response()->json(['message' => 'Producto creado exitosamente', 'articulo' => $articulo], 201);
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
