<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Kiosco extends Model
{
    use HasFactory;

    // Tabla asociada
    protected $table = 'Kioscos';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'num_serie',
        'datafono_id',
        'cliente_id',
        'nombre',
    ];

    public static function getKioscosByClienteId($cliente_id)
    {
        // Validamos que $cliente_id sea un número válido
        if (!is_numeric($cliente_id)) {
            return [];
        }

        return self::where('cliente_id', $cliente_id)
            ->get()
            ->toArray();
    }

    // Relación con cliente (si aplica)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relación con datafono (si aplica)
    public function datafono()
    {
        return $this->belongsTo(Datafono::class);
    }
}
