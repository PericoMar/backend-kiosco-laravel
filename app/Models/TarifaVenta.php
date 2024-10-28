<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaVenta extends Model
{
    use HasFactory;

    // Especifica la tabla si no sigue la convención de nombres plural
    protected $table = 'Tarifa_venta';

    public $timestamps = false;

    // Especifica los campos que son asignables
    protected $fillable = [
        'precio_venta', 
        'tipo_tarifa_id', 
        'articulo_id'
    ];

    // Define las relaciones (si es necesario)
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }
}
