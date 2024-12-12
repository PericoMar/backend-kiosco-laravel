<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('Usuarios')->insert(
            [
                'usuario' => 'eduardokong',
                'contraseña' => Hash::make('user12'),
                'nombre' => 'Eduardo',
                'rol' => 'comercial',
                'created_at' => now(),
                'updated_at' => now(),
                'cliente_id' => 1,
            ]);
    }
}
