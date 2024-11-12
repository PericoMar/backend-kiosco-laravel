<?php

namespace App\Http\Controllers;

use App\Models\OpcionPreguntaArticulo;
use App\Models\Articulo;
use App\Models\TarifaVenta;
use Illuminate\Http\Request;

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
                'price' => 'required|numeric',
                'img' => 'required|string', // Suponiendo que es la ruta de la imagen
                'questionId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'nullable|integer',
                'iva' => 'required|integer'
            ]);

            // Crear una nueva instancia del modelo Articulo
            $articulo = new Articulo();
            $articulo->articulo = $validatedData['name'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->imagen = $validatedData['img'];
            $articulo->estado = $validatedData['status'];
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
                'price' => 'required|numeric',
                'img' => 'required|string',
                'questionId' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'nullable|integer',
                'iva' => 'required|integer'
            ]);

            // Encontrar el artículo existente
            $articulo = Articulo::findOrFail($id);

            // Actualizar los datos del artículo
            $articulo->articulo = $validatedData['name'];
            $articulo->descripcion = $validatedData['description'];
            $articulo->imagen = $validatedData['img'];
            $articulo->estado = $validatedData['status'];
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

            // Actualizar o crear la relación en OpcionPreguntaArticulo
            $opcion = OpcionPreguntaArticulo::firstOrNew([
                'pregunta_articulo_id' => $validatedData['questionId'],
                'articulo_id' => $articulo->id
            ]);
            $opcion->suplemento = null;
            
            // Guardar la opción en la base de datos
            $opcion->save();

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
        $opcion = OpcionPreguntaArticulo::findOrFail($id);
        $opcion->delete();
        return response()->json(null, 204);
    }
}
