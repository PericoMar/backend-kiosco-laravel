<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpresoraFamilia extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'Impresoras_Familias';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'id_impresora', 
        'id_familia',
        'created_at',
        'updated_at',
    ];

    // Deshabilitar las marcas de tiempo si no son necesarias
    public $timestamps = false;

    /**
     * Relación con el modelo Impresora
     */
    public function impresora()
    {
        return $this->belongsTo(Impresora::class, 'id_impresora');
    }

    /**
     * Relación con el modelo Familia
     */
    public function familia()
    {
        return $this->belongsTo(Familia::class, 'id_familia');
    }
}
