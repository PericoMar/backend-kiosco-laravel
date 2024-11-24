<?php

namespace App\Http\Controllers;

use App\Models\OpcionPreguntaArticulo;
use App\Models\Articulo;
use App\Models\TarifaVenta;
use Illuminate\Http\Request;
use App\Models\Alergeno;

class OpcionPreguntaArticuloController extends Controller
{


    public function index()
    {
        $opciones = OpcionPreguntaArticulo::all();
        return response()->json($opciones);
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
                'questionId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'nullable|integer',
                'iva' => 'required|integer'
            ]);

            // Crear una nueva instancia del modelo Articulo
            $articulo = new Articulo();
            $articulo->articulo = $validatedData['name'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->estado = $validatedData['status'];
            $articulo->visible_TPV = true;
            $articulo->tipo_iva_id = $validatedData['iva'];

            // Guardar el nuevo artículo en la base de datos
            $articulo->save();

            // Guardar los precios en la tabla Tarifa_venta
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

            $opcion = new OpcionPreguntaArticulo();
            $opcion->pregunta_articulo_id = $validatedData['questionId'];
            $opcion->articulo_id = $articulo->id;
            $opcion->suplemento = null;

            $opcion->save();

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
        $opcion = OpcionPreguntaArticulo::findOrFail($id);
        return response()->json($opcion);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'price_1' => 'required|numeric',
                'price_2' => 'nullable|numeric',
                'price_3' => 'nullable|numeric',
                'questionId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'nullable|integer',
                'iva' => 'required|integer',
                'allergens' => 'nullable|array'
            ]);

            $id = OpcionPreguntaArticulo::findOrFail($id)->articulo_id;

            $alergenos = $validatedData['allergens'] ?? [];

            // Encontrar el artículo existente
            $articulo = Articulo::findOrFail($id);

            // Actualizar los datos del artículo
            $articulo->articulo = $validatedData['name'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->estado = $validatedData['status'];
            $articulo->visible_TPV = true;
            $articulo->tipo_iva_id = $validatedData['iva'];
            
            self::actualizarAlergenos($alergenos, $articulo);

            // Guardar cambios en la base de datos
            $articulo->save();

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

            // Actualizar o crear la relación en OpcionPreguntaArticulo
            $opcion = OpcionPreguntaArticulo::firstOrNew([
                'pregunta_articulo_id' => $validatedData['questionId'],
                'articulo_id' => $articulo->id
            ]);
            $opcion->suplemento = null;
            
            // Guardar la opción en la base de datos
            $opcion->save();

            return response()->json(['message' => 'Modificador actualizado exitosamente', 'id' => $articulo->id], 200);
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
        $opcion = OpcionPreguntaArticulo::findOrFail($id);
        $opcion->delete();
        return response()->json(null, 204);
    }
}
