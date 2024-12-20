<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KioscosSeeder extends Seeder
{
    public function run()
    {
        DB::table('Kioscos')->insert([
            [
                'num_serie' => 'helados',
                'created_at' => Carbon::now()->format('Y-m-d\TH:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d\TH:i:s'),
                'datafono_id' => 1,
                'cliente_id' => 2,
                'nombre' => 'Kiosco Heladeria',
            ]
        ]);
    }
}
