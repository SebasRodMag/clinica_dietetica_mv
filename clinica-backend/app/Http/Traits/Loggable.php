<?php

namespace App\Http\Traits;

use App\Models\Log;

trait Loggable
{
    /**
     * Registrar una acción en los logs.
     *
     * @param int|null $userId ID del usuario que realiza la acción
     * @param string $accion Acción realizada
     * @param string|null $descripcion Descripción de la acción
     * @param string|null $columnaAfectada Columna afectada por la acción
     * @return void No devuelve nada, solo registra el log.
     */
    public function registrarLog(?int $userId, string $accion, ?string $descripcion = null, ?string $columnaAfectada = null): void
    {
        Log::create([
            'user_id' => $userId,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'columna_afectada' => $columnaAfectada,
        ]);
    }
}
