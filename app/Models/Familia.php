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
    
    public function printers()
    {
        return $this->belongsToMany(
            Impresora::class,         // Modelo de las impresoras
            'impresoras_familias',  // Nombre de la tabla pivot
            'id_familia',           // Clave foránea de la familia en la tabla pivot
            'id_impresora'          // Clave foránea de la impresora en la tabla pivot
        );
    }

    public static function getFamilies($cliente_id)
    {
        return self::with(['printers:id']) // Incluye solo el campo id de las impresoras
            ->select(
                'id',
                'codigo as name',
                DB::raw("CONCAT('" . env('APP_URL') . "', imagen) as img"),
                'descripcion as desc'
            )
            ->where('cliente_id', $cliente_id)
            ->get()
            ->map(function ($family) {
                // Convierte la relación printers a un array plano de IDs
                $family->printers = $family->printers->pluck('id')->toArray();
                return $family;
            })
            ->toArray();
    }


}
