<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cita extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'citas';
    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'id_paciente',
        'id_especialista',
        'fecha_hora_cita',
        'tipo_cita',
        'estado',
        'es_primera',
        'comentario',
    ];

    protected $casts = [
        'es_primera' => 'boolean',
        'fecha_hora_cita' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function especialista()
    {
        return $this->belongsTo(Especialista::class, 'id_especialista');
    }

    
    public function getEsRealizadaAttribute()
    {
        return $this->estado === 'realizada';
    }
	public function esTelematica()
	{
		return $this->tipo_cita === 'telemática';
	}
}