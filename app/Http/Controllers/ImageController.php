<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    /**
     * Subir una imagen y guardar la URL en una columna específica de la base de datos.
     *
     * @param Request $request
     * @param string $tableName
     * @param int $recordId
     * @param string $columnName
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request, string $tableName, int $recordId, string $columnName)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,jfif|max:2048', // Reglas de validación
        ]);

        // Verificar que la columna existe en la tabla
        if (!\Schema::hasColumn($tableName, $columnName)) {
            return response()->json([
                'success' => false,
                'message' => "La columna '$columnName' no existe en la tabla '$tableName'.",
            ], 400);
        }

        // Obtener la URL actual de la imagen desde la base de datos
        $currentImage = DB::table($tableName)->where('id', $recordId)->value($columnName);

        // Eliminar la imagen actual si existe
        if ($currentImage && Storage::exists(str_replace('/storage', 'public', $currentImage))) {
            Storage::delete(str_replace('/storage', 'public', $currentImage));
        }

        // Guardar la nueva imagen en la carpeta específica
        $folder = $tableName;
        $path = $request->file('image')->store("public/$folder");

        // Convertir la ruta al formato URL accesible
        $url = Storage::url($path);

        // Actualizar la base de datos con la nueva URL de la imagen
        DB::table($tableName)->where('id', $recordId)->update([$columnName => $url]);

        return response()->json([
            'success' => true,
            'message' => 'Imagen subida y URL guardada correctamente.',
            'url' => $url,
        ]);
    }

}
