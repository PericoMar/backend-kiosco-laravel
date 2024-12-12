<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Familia extends Model
{
    use HasFactory;

    protected $table = 'familias';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'orden',
        'visible_TPV',
        'estado',
        'imagen'
    ];

    public static function getFamilies($cliente_id)
    {
        return self::select(
            'id',
            'codigo as name',
            DB::raw("CONCAT('" . env('APP_URL') . "', imagen) as img"),
            'descripcion as desc'
        )
        ->where('cliente_id', $cliente_id)
        ->get()
        ->toArray();
    }

}
