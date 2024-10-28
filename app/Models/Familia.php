<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function getFamilies()
    {
        return self::select('id', 'codigo as name', 'imagen as img')->get()->toArray();
    }
}
