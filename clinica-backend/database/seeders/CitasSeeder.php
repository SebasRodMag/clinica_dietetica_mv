<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Especialista;

class CitasSeeder extends Seeder
{
    public function run()
    {
        $citasACrear = 200;
        $intentosMaximos = 1000;
        $creadas = 0;
        $intentos = 0;

        while ($creadas < $citasACrear && $intentos < $intentosMaximos) {
            $intentos++;

            $paciente = Paciente::inRandomOrder()->first();
            $especialista = Especialista::inRandomOrder()->first();

            if (!$paciente || !$especialista) {
                $this->command->warn("No hay suficientes pacientes o especialistas para crear citas.");
                break;
            }

            $esPrimera = !Cita::where('id_paciente', $paciente->id)->exists();

            $cita = Cita::factory()->make([
                'id_paciente' => $paciente->id,
                'id_especialista' => $especialista->id,
                'estado' => 'pendiente',
                'es_primera' => $esPrimera,
            ]);

            if (!$this->existeCitaDuplicada($cita)) {
                $cita->save();
                $creadas++;
            }
        }

        if ($creadas < $citasACrear) {
            $this->command->warn("Solo se pudieron crear {$creadas} citas después de {$intentos} intentos.");
        } else {
            $this->command->info("Se crearon {$creadas} citas correctamente.");
        }
    }

    private function existeCitaDuplicada(Cita $cita)
    {
        return Cita::where('id_paciente', $cita->id_paciente)
            ->where('id_especialista', $cita->id_especialista)
            ->where('fecha_hora_cita', $cita->fecha_hora_cita)
            ->exists();
    }
}
