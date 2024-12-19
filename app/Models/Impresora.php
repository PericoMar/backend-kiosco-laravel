<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    use HasFactory;

    // Define la tabla asociada (opcional si el nombre sigue las convenciones)
    protected $table = 'Impresoras';

    // Define los campos que pueden ser llenados de forma masiva
    protected $fillable = [
        'nombre',
        'impresora_ip',
        'estado',
        'descripcion',
        'cliente_id',
    ];

    // Si no usas timestamps en esta tabla, descomenta la siguiente línea
    // public $timestamps = false;

    // Si las marcas de tiempo no siguen el formato estándar de Laravel
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getImpresorasByClienteId($cliente_id)
    {
        // Validamos que $cliente_id sea un número válido
        if (!is_numeric($cliente_id)) {
            return [];
        }

        return self::where('cliente_id', $cliente_id)
            ->get()
            ->toArray();
    }
}