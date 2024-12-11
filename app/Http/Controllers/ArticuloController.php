<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TarifaVenta;
use App\Models\PreguntaArticulo;
use App\Models\OpcionPreguntaArticulo;
use App\Models\Alergeno;

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


        $questions = DB::table('Preguntas_Articulo')->get();

        $options = DB::table('Opciones_Preguntas_Articulo')->get();
        
        $productsWithCustomizations = $products->map(function($product) use ($questions, $options) {
            // Filtrar las preguntas que corresponden a este artículo
            $filteredQuestions = $questions->filter(function($question) use ($product) {
                return $question->articulo_id == $product->id;
            });

            $alergenos = DB::table('Articulos_Alergenos')
                ->join('Alergenos', 'Articulos_Alergenos.alergeno_id', '=', 'Alergenos.id')
                ->where('Articulos_Alergenos.articulo_id', $product->id)
                ->pluck('Alergenos.nombre')
                ->toArray();

            $iva = DB::table('Tipos_Iva')->where('id', $product->tipo_iva_id)->value('iva_porcentaje') ?? 0;
            // Preguntas y opciones correspondientes al artículo
            $customizationQuestions = $filteredQuestions->map(function($question) use ($options) {
                return [
                    'id' => $question->id,
                    'name' => $question->texto,
                    'questionType' => $question->tipo_pregunta ?? "single",
                    'status' => $question->estado == 1 ? 'Habilitado' : 'Deshabilitado',
                    'maxChoices' => $question->unidades_maximas,
                    'minChoices' => $question->unidades_minimas,
                    'options' => $options->filter(function($option) use ($question) {
                        // Filtrar las opciones que pertenecen a la pregunta
                        return $option->pregunta_articulo_id == $question->id;
                    })->map(function($option) {
                        // Busca el producto asociado
                        // Coger el producto con el id $option->id
                        $alergenos = DB::table('Articulos_Alergenos')
                            ->join('Alergenos', 'Articulos_Alergenos.alergeno_id', '=', 'Alergenos.id')
                            ->where('Articulos_Alergenos.articulo_id', $option->articulo_id)
                            ->pluck('Alergenos.nombre')
                            ->toArray();
                        $modifier = DB::table('Articulos')->where('id', $option->articulo_id)->first();
                        $tarifa_venta = DB::table('Tarifa_Venta')->where('articulo_id', $option->articulo_id)->first();
                        return [
                            'id' => $option->id,
                            'value' => $modifier->articulo,
                            'img' => $modifier->imagen ? env('APP_URL') . $modifier->imagen : null, // Si tiene una imagen, se asigna
                            'prices' => [$option->suplemento ?? $tarifa_venta->precio_venta],  // Si tiene un precio, se asigna
                            'allergens' => $alergenos
                        ];
                    })->values()->toArray()
                ];
            });
            // Retornar el producto
            return [
                'id' => $product->id,
                'name' => $product->articulo,
                'prices' => [$product->precio],
                'status' => $product->estado ? 'Habilitado' : 'Deshabilitado',
                'taxes' => $iva,
                'allergens' => $alergenos,
                'img' => $product->imagen ? env('APP_URL') . $product->imagen : null,
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
                'price_1' => 'required|numeric',
                'price_2' => 'nullable|numeric',
                'price_3' => 'nullable|numeric',
                'familyId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'required|integer',
                'iva' => 'required|integer'
            ]);

            // Crear una nueva instancia del modelo Articulo
            $articulo = new Articulo();
            $articulo->articulo = $validatedData['name'];
            $articulo->familia_id = $validatedData['familyId'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->estado = $validatedData['status'];
            $articulo->visible_TPV = true; 
            $articulo->tipo_iva_id = $validatedData['iva'];

            // Guardar el nuevo artículo en la base de datos
            $articulo->save();

            $prices = [
                1 => $validatedData['price_1'],
                2 => $validatedData['price_2'],
                3 => $validatedData['price_3']
            ];

            foreach ($prices as $tipoTarifaId => $price) {
                if ($price !== null) { // Verificar que el precio no sea null
                    $tarifaVenta = new TarifaVenta();
                    $tarifaVenta->precio_venta = $price;
                    $tarifaVenta->tipo_tarifa_id = $tipoTarifaId;
                    $tarifaVenta->articulo_id = $articulo->id;
                    $tarifaVenta->save();
                }
            }

            self::actualizarAlergenos($request->allergens, $articulo);

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
                $option = OpcionPreguntaArticulo::find($id);
                if (!$option) {
                    return response()->json(['error' => 'Modificador no encontrado'], 404);
                }

                $modifier = Articulo::find($option->articulo_id);

    
                // Obtener las tres primeras tarifas del modificador
                $modifierTarifas = DB::table('Tarifa_venta')
                    ->where('articulo_id', $option->articulo_id)
                    ->orderBy('id')
                    ->take(3)
                    ->pluck('precio_venta')
                    ->toArray();
    
                // Obtener alérgenos del modificador
                $modifierAlergenos = DB::table('Articulos_Alergenos')
                    ->join('Alergenos', 'Articulos_Alergenos.alergeno_id', '=', 'Alergenos.id')
                    ->where('Articulos_Alergenos.articulo_id', $option->articulo_id)
                    ->pluck('Alergenos.nombre')
                    ->toArray();
    
                return response()->json([
                    'productType' => 'Modificador',
                    'name' => $modifier->articulo,
                    'img' => $modifier->imagen,
                    'price_1' => $modifierTarifas[0] ?? null,
                    'price_2' => $modifierTarifas[1] ?? null,
                    'price_3' => $modifierTarifas[2] ?? null,
                    'family' => $option->pregunta_articulo_id,
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
                'name' => 'nullable|string|max:255',
                'price_1' => 'nullable|numeric',
                'price_2' => 'nullable|numeric',
                'price_3' => 'nullable|numeric',
                'familyId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'required|integer',
                'iva' => 'required|integer',
                'allergens' => 'nullable|array'
            ]);

            // Encontrar el artículo existente
            $articulo = Articulo::findOrFail($id);



            // Actualizar los datos del artículo
            $articulo->articulo = $validatedData['name'];
            $articulo->familia_id = $validatedData['familyId'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->estado = $validatedData['status'];
            $articulo->visible_TPV = true;
            $articulo->tipo_iva_id = $validatedData['iva'];
            $alergenos = $validatedData['allergens'] ?? [];

            // Guardar cambios en la base de datos
            $articulo->save();

            // Crear o actualizar cada tipo de tarifa si el precio no es null
            $prices = [
                1 => $validatedData['price_1'],
                2 => $validatedData['price_2'],
                3 => $validatedData['price_3']
            ];

            foreach ($prices as $tipoTarifaId => $price) {
                if ($price !== null) { // Verificar que el precio no sea null
                    $tarifaVenta = TarifaVenta::firstOrNew([
                        'articulo_id' => $articulo->id,
                        'tipo_tarifa_id' => $tipoTarifaId
                    ]);
                    $tarifaVenta->precio_venta = $price;
                    $tarifaVenta->save();
                }
            }

            self::actualizarAlergenos($alergenos, $articulo);

            return response()->json(['message' => 'Producto actualizado exitosamente', 'id' => $articulo->id], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['message' => 'Error de validación', 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return response()->json(['message' => 'Error al actualizar el producto', 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizarAlergenos($alergenos, Articulo $articulo)
    {

        // Filtrar los alérgenos eliminando cadenas vacías
        $alergenos = array_filter($alergenos, function($alergeno) {
            return $alergeno !== '';
        });

        // Obtener los IDs de los alérgenos basados en los nombres
        $alergenoIds = Alergeno::whereIn('nombre', $alergenos)->pluck('id')->toArray();

        // Eliminar los alérgenos actuales del artículo
        $articulo->alergenos()->detach();

        // Asociar los nuevos alérgenos
        $articulo->alergenos()->attach($alergenoIds);

        return response()->json(['success' => true, 'message' => 'Alérgenos actualizados correctamente.']);
    }



    public function destroy($id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->delete();

        // Eliminar las tarifas de venta asociadas al artículo
        TarifaVenta::where('articulo_id', $id)->delete();

        return response()->json(null, 204);
    }
}
