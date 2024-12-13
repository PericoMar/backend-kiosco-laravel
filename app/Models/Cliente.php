<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // Tabla asociada
    protected $table = 'Clientes';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'estado_suscripcion',
        'fecha_alta',
        'fecha_baja',
        'num_kioscos'
    ];

}
