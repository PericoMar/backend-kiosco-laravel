<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreguntaArticulo extends Model
{
    use HasFactory;

    protected $table = 'preguntas_articulo';

    public $timestamps = false;

    protected $fillable = [
        'orden',
        'texto',
        'articulo_id',
        'tipo_pregunta',
        'unidades_maximas',
        'unidades_minimas'
    ];

    // Relación con el modelo Articulo
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }
}
