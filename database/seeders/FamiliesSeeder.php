<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamiliesSeeder extends Seeder
{
    public function run()
    {
        DB::table('familias')->insert([
            [
                'codigo' => 'FAM001',
                'orden' => 1,
                'visible_TPV' => false,
                'estado' => 1, // Cambia 'Activo' a 1
                'imagen' => '../../../assets/pizza.png',
            ],
            [
                'codigo' => 'FAM002',
                'orden' => 2,
                'visible_TPV' => false,
                'estado' => 1, // Cambia 'Activo' a 1
                'imagen' => '../../../assets/bebidas.png',
            ],
            [
                'codigo' => 'FAM003',
                'orden' => 3,
                'visible_TPV' => false,
                'estado' => 1, // Cambia 'Activo' a 1
                'imagen' => '../../../assets/burguer.png',
            ],
            [
                'codigo' => 'FAM004',
                'orden' => 4,
                'visible_TPV' => false,
                'estado' => 1, // Cambia 'Activo' a 1
                'imagen' => '../../../assets/postre.png',
            ],
        ]);
    }
}

