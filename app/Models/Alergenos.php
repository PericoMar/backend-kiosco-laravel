<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Articulo;

class Alergeno extends Model
{
    public function articulos(): BelongsToMany
    {
        return $this->belongsToMany(Articulo::class, 'articulos_alergenos', 'alergeno_id', 'articulo_id');
    }
}
