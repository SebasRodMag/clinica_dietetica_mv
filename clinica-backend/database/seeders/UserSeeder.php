<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Especialista;

class UserSeeder extends Seeder
{
    private $dniUsados = [];
    private $telefonoUsados = [];

    public function run()
    {
        $faker = Faker::create('es_ES');

        $cantidadUsuario = 10;
        $cantidadAdministrador = 2;
        $cantidadPaciente = 200;
        $cantidadEspecialista = 10;

        $this->crearUsuariosConRol($cantidadUsuario, 'usuario', $faker);
        $this->crearUsuariosConRol($cantidadAdministrador, 'administrador', $faker);
        $this->crearUsuariosConRol($cantidadPaciente, 'paciente', $faker);
        $this->crearUsuariosConRol($cantidadEspecialista, 'especialista', $faker);
    }

    private function crearUsuariosConRol(int $cantidad, string $rol, $faker)
    {
        for ($i = 1; $i <= $cantidad; $i++) {
            $dni = $this->generarDniUnico();
            $telefono = $this->generarTelefonoUnico();

            $nombre = $faker->firstName();
            $apellidos = $faker->lastName();

            $user = User::create([
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => "{$rol}{$i}@correo.com",
                'password' => bcrypt('password'),
                'dni_usuario' => $dni,
                'telefono' => $telefono,
                'direccion' => $faker->address,
            ]);

            $user->assignRole($rol);

            if ($rol === 'paciente') {
                Paciente::create([
                    'user_id' => $user->id,
                    'numero_historial' => strtoupper(Str::random(10)),
                    'fecha_alta' => $faker->dateTimeBetween('-2 years', 'now'),
                    'fecha_baja' => null,
                ]);
            } elseif ($rol === 'especialista') {
                Especialista::create([
                    'user_id' => $user->id,
                    'telefono' => $telefono,
                    'especialidad' => $faker->randomElement(['Nutrición', 'Endocrinología', 'Medicina General'])
                ]);
            }
        }
    }

    private function generarDniUnico(): string
    {
        do {
            $dni = $this->generarDni();
        } while (in_array($dni, $this->dniUsados));

        $this->dniUsados[] = $dni;
        return $dni;
    }

    private function generarTelefonoUnico(): string
    {
        do {
            $telefono = $this->generarTelefono();
        } while (in_array($telefono, $this->telefonoUsados));

        $this->telefonoUsados[] = $telefono;
        return $telefono;
    }

    private function generarDni(): string
    {
        //Generar DNI
        $numero = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $pos = intval($numero) % 23;
        $letra = $letras[$pos];
        return $numero . $letra;
    }

    private function generarTelefono(): string
    {
        //Generar móvil
        $prefijo = rand(6, 7);
        $resto = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        return $prefijo . $resto;
    }
}
