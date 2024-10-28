<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionPreguntaArticulo extends Model
{
    use HasFactory;

    protected $table = 'opciones_preguntas_articulo';

    public $timestamps = false;

    protected $fillable = [
        'pregunta_articulo_id',
        'orden',
        'articulo_id',
        'suplemento'
    ];

    // Relación con PreguntaArticulo
    public function pregunta()
    {
        return $this->belongsTo(PreguntaArticulo::class, 'pregunta_articulo_id');
    }

    // Relación con Articulo
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }
}
