<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $table = 'articulos';

    // Si necesitas añadir más atributos masivos
    protected $fillable = [
        'articulo',
        'codigo',
        'familia_id',
        'estado',
        'visible_TPV',
        'tipo_iva_id',
        'imagen'
    ];
}
