<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Especialista;
use Faker\Factory as Faker;

class EspecialistaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $usuariosEspecialistas = User::role('especialista')->get();

        foreach ($usuariosEspecialistas as $usuario) {
            $especialistaExistente = Especialista::where('user_id', $usuario->id)->first();

            if (!$especialistaExistente) {
                Especialista::create([
                    'user_id' => $usuario->id,
                    'especialidad' => $faker->randomElement(['Nutrición', 'Endocrinología', 'Medicina General']),

                ]);
            }
        }
    }
}
