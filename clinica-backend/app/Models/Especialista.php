<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Especialista extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'especialidad',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pacientes()
{
    return $this->hasManyThrough(
        Paciente::class,
        Cita::class,
        'id_especialista',
        'id_paciente',
        'id',
        'id'
    )->distinct();
}

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * Actualiza los campos del especialista con los datos proporcionados.
     *
     * @param array $datos datos del especialista a actualizar
     * @return bool true si se actualizó correctamente, false en caso de error
     */
    public function actualizarEspecialista(array $datos): bool
    {
        //Rellena los atributos del modelo con los datos dados
        $this->fill($datos);

        //Guarda y retorna true si fue exitoso, false si falla
        return $this->save();
    }
}
