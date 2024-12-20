<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('Clientes')->insert(
            [
                'nombre' => 'Kong Software 2',
                'email' => 'desarrollo@kongconsulting.es',
                'telefono' => '66666666666',
                'estado_suscripcion' => 1,
                'fecha_alta' => now(),
                'fecha_baja' => null,
                'num_kioscos' => 100,
            ]);
    }
}