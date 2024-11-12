<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Alergeno;

class Articulo extends Model
{
    use HasFactory;

    protected $table = 'articulos';

    public $timestamps = false;

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

    public function alergenos(): BelongsToMany
    {
        return $this->belongsToMany(Alergeno::class, 'articulos_alergenos', 'articulo_id', 'alergeno_id');
    }
}
