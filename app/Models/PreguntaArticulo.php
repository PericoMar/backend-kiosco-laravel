<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreguntaArticulo extends Model
{
    use HasFactory;

    protected $table = 'preguntas_articulo';

    protected $fillable = [
        'orden',
        'texto',
        'articulo_id',
        'tipo_pregunta',
        'unidades_maximas'
    ];

    // Relación con el modelo Articulo
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }
}
