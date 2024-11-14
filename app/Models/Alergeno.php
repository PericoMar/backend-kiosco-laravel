<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Articulo;

class Alergeno extends Model
{   
    use HasFactory;

    protected $table = 'Alergenos';

    public $timestamps = false;

    // Si necesitas añadir más atributos masivos
    protected $fillable = [
        'id',
        'nombre'
    ];

    public function articulos(): BelongsToMany
    {
        return $this->belongsToMany(Articulo::class, 'articulos_alergenos', 'alergeno_id', 'articulo_id');
    }
}
