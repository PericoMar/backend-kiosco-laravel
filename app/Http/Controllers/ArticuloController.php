<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TarifaVenta;
use App\Models\PreguntaArticulo;

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
                    'status' => $question->estado ? 'Habilitado' : 'Deshabilitado',
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
                            'price' => $option->suplemento ?? $tarifa_venta->precio_venta  // Si tiene un precio, se asigna
                        ];
                    })->values()->toArray()
                ];
            });
            // Retornar el producto
            return [
                'id' => $product->id,
                'name' => $product->articulo,
                'price' => $product->precio,
                'status' => $product->estado ? 'Habilitado' : 'Deshabilitado',
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
                'img' => 'required|string',
                'familyId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'required|boolean',
                'iva' => 'required|integer'
            ]);

            // Crear una nueva instancia del modelo Articulo
            $articulo = new Articulo();
            $articulo->articulo = $validatedData['name'];
            $articulo->familia_id = $validatedData['familyId'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->imagen = $validatedData['img'];
            $articulo->estado = $validatedData['status'] == 'Habilitado' ? 1 : 0;
            $articulo->visible_TPV = true; 
            $articulo->tipo_iva_id = $validatedData['iva'];

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



    public function show($productType, $id)
    {
        switch ($productType) {
            case 'Producto':
                // Obtener el producto por ID
                $product = Articulo::find($id);
                if (!$product) {
                    return response()->json(['error' => 'Producto no encontrado'], 404);
                }
    
                // Obtener las tres primeras tarifas
                $tarifas = DB::table('Tarifa_venta')
                    ->where('articulo_id', $id)
                    ->orderBy('id')
                    ->take(3)
                    ->pluck('precio_venta')
                    ->toArray();
    
                // Obtener los alérgenos relacionados con el artículo
                $alergenos = DB::table('Articulos_Alergenos')
                    ->join('Alergenos', 'Articulos_Alergenos.alergeno_id', '=', 'Alergenos.id')
                    ->where('Articulos_Alergenos.articulo_id', $id)
                    ->pluck('Alergenos.nombre')
                    ->toArray();
    
                // Preparar la respuesta
                return response()->json([
                    'productType' => 'Producto',
                    'name' => $product->articulo,
                    'img' => $product->imagen,
                    'price_1' => $tarifas[0] ?? null,
                    'price_2' => $tarifas[1] ?? null,
                    'price_3' => $tarifas[2] ?? null,
                    'family' => $product->familia_id,
                    'status' => $product->estado ? 'Habilitado' : 'Deshabilitado',
                    'descripcion' => $product->descripcion,
                    'min' => null,
                    'max' => null,
                    'iva' => $product->tipo_iva_id,
                    'allergens' => $alergenos
                ]);
    
            case 'Grupo de modificadores':
                $modifierGroup = PreguntaArticulo::find($id);
                if (!$modifierGroup) {
                    return response()->json(['error' => 'Grupo de modificadores no encontrado'], 404);
                }
    
                return response()->json([
                    'productType' => 'Grupo de modificadores',
                    'name' => $modifierGroup->texto,
                    'price_1' => null,
                    'price_2' => null,
                    'price_3' => null,
                    'family' => $modifierGroup->articulo_id,
                    'status' => $modifierGroup->estado ? 'Habilitado' : 'Deshabilitado',
                    'descripcion' => $modifierGroup->descripcion,
                    'min' => $modifierGroup->unidades_minimas,
                    'max' => $modifierGroup->unidades_maximas,
                    'iva' => null,
                    'allergens' => []
                ]);
    
            case 'Modificador':
                $modifier = Articulo::find($id);
                if (!$modifier) {
                    return response()->json(['error' => 'Modificador no encontrado'], 404);
                }
    
                // Obtener las tres primeras tarifas del modificador
                $modifierTarifas = DB::table('Tarifa_venta')
                    ->where('articulo_id', $id)
                    ->orderBy('id')
                    ->take(3)
                    ->pluck('precio_venta')
                    ->toArray();
    
                // Obtener alérgenos del modificador
                $modifierAlergenos = DB::table('Articulos_Alergenos')
                    ->join('Alergenos', 'Articulos_Alergenos.alergeno_id', '=', 'Alergenos.id')
                    ->where('Articulos_Alergenos.articulo_id', $id)
                    ->pluck('Alergenos.nombre')
                    ->toArray();
    
                return response()->json([
                    'productType' => 'Modificador',
                    'name' => $modifier->articulo,
                    'img' => $product->imagen,
                    'price_1' => $modifierTarifas[0] ?? null,
                    'price_2' => $modifierTarifas[1] ?? null,
                    'price_3' => $modifierTarifas[2] ?? null,
                    'family' => DB::table('Opciones_Preguntas_Articulo')
                                  ->where('pregunta_articulo_id', $id)
                                  ->value('articulo_id'),
                    'status' => $modifier->estado ? 'Habilitado' : 'Deshabilitado',
                    'descripcion' => $modifier->descripcion,
                    'min' => null,
                    'max' => null,
                    'iva' => $modifier->tipo_iva_id,
                    'allergens' => $modifierAlergenos
                ]);
    
            default:
                return response()->json(['error' => 'Tipo de producto no válido'], 400);
        }
    }
    


    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'img' => 'required|string',
                'familyId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'required|boolean',
                'iva' => 'required|integer'
            ]);

            // Encontrar el artículo existente
            $articulo = Articulo::findOrFail($id);

            // Actualizar los datos del artículo
            $articulo->articulo = $validatedData['name'];
            $articulo->familia_id = $validatedData['familyId'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->imagen = $validatedData['img'];
            $articulo->estado = $validatedData['status'] == 'Habilitado' ? 1 : 0;
            $articulo->visible_TPV = true;
            $articulo->tipo_iva_id = $validatedData['iva'];
            
            // Guardar cambios en la base de datos
            $articulo->save();

            // Actualizar o crear el precio en la tabla Tarifa_venta
            $tarifaVenta = TarifaVenta::firstOrNew([
                'articulo_id' => $articulo->id,
                'tipo_tarifa_id' => 1
            ]);
            $tarifaVenta->precio_venta = $validatedData['price'];
            
            // Guardar la tarifa en la base de datos
            $tarifaVenta->save();

            return response()->json(['message' => 'Producto actualizado exitosamente', 'articulo' => $articulo], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['message' => 'Error de validación', 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return response()->json(['message' => 'Error al actualizar el producto', 'error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->delete();
        return response()->json(null, 204);
    }
}
