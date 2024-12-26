<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use App\Models\ImpresoraFamilia;

class FamiliaController extends Controller
{

    public function index($cliente_id): JsonResponse
    {
        // Este metodo ya cambia los nombres a los mismos que el front espera
        $families = Familia::getFamilies($cliente_id);
        return response()->json($families);
    }


    public function store(Request $request, $cliente_id)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|integer',
                'desc' => 'nullable|string',
                'printers' => 'nullable|array',
            ]);


            $familia = new Familia();
            $familia->codigo = $validatedData['name'];
            $familia->estado = $validatedData['status']; 
            $familia->descripcion = $validatedData['desc']; 
            $familia->visible_TPV = true; // O false, según sea necesario
            $familia->cliente_id = $cliente_id;

            // Guardar el nuevo artículo en la base de datos
            $familia->save();

            if ($request->has('printers')) {
                foreach ($request->printers as $printerId) {
                    ImpresoraFamilia::create([
                        'id_impresora' => $printerId,
                        'id_familia' => $familia->id,
                    ]);
                }
            }


            return response()->json(['message' => 'Producto creado exitosamente', 'familia' => $familia], 201);
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

        $familia->printers = ImpresoraFamilia::where('id_familia', $familia->id)->pluck('id_impresora');

        return response()->json([
            'productType' => 'Producto',
            'name' => $familia->codigo,
            'img' => $familia->imagen,
            'status' => $familia->estado ? 'Habilitado' : 'Deshabilitado',
            'desc' => $familia->descripcion,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|integer',
            'desc' => 'nullable|string',
            'printers' => 'nullable|array',
        ]);


        $familia = Familia::findOrFail($id);
        
        $familia->codigo = $validatedData['name'];
        $familia->estado = $validatedData['status']; 
        $familia->descripcion = $validatedData['desc']; 
        $familia->visible_TPV = true; // O false, según sea necesario

        $familia->save();

        // Limpiar las impresoras asociadas
        ImpresoraFamilia::where('id_familia', $familia->id)->delete();

        if ($request->has('printers')) {
            foreach ($request->printers as $printerId) {
                ImpresoraFamilia::create([
                    'id_impresora' => $printerId,
                    'id_familia' => $familia->id,
                ]);
            }
        }

        return response()->json(["familia" => $id]);
    }

    public function destroy($id)
    {
        $familia = Familia::findOrFail($id);
        $familia->delete();
        return response()->json(null, 204);
    }
}
