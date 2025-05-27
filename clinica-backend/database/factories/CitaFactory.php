<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Especialista;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class CitaFactory extends Factory
{
    protected $model = Cita::class;

    protected $festivos = [
        '2025-01-01',
        '2025-05-01',
        '2025-12-25',
    ];

    public function definition(): array
	{
		$paciente = Paciente::inRandomOrder()->first();
		$especialista = Especialista::inRandomOrder()->first();

		if (!$paciente || !$especialista) {
			throw new \Exception("No hay suficientes pacientes o especialistas para generar cita.");
		}

		$fecha = $this->obtenerFechaValida();

		$horaInicio = Carbon::createFromTime(8, 0);
		$bloque = rand(0, 13);
		$hora = $horaInicio->copy()->addMinutes($bloque * 30);

		$fechaHora = $fecha->copy()->setTimeFromTimeString($hora->format('H:i:s'));

		return [
			'id_paciente' => $paciente->id,
			'id_especialista' => $especialista->id,
			'fecha_hora_cita' => $fechaHora,
			'estado' => 'pendiente',
			'comentario' => $this->faker->sentence(),
			'es_primera' => true,
			'tipo_cita' => $this->faker->randomElement(['presencial', 'telemática']),
		];
	}

    public function primera()
    {
        return $this->state(function (array $attributes) {
            return [
                'es_primera' => true,
            ];
        });
    }

    private function obtenerFechaValida(): Carbon
    {
        $fecha = Carbon::now()->addDays(rand(0, 30))->startOfDay();

        while ($this->esFinDeSemana($fecha) || $this->esFestivo($fecha)) {
            $fecha->addDay();
        }

        return $fecha;
    }

    private function esFinDeSemana(Carbon $fecha): bool
    {
        return $fecha->isWeekend();
    }

    private function esFestivo(Carbon $fecha): bool
    {
        return in_array($fecha->toDateString(), $this->festivos);
    }
}
