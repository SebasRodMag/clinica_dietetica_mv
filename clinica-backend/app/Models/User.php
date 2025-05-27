<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
        'nombre',
		'apellidos',
		'dni_usuario',
		'email',
		'fecha_nacimiento',
		'telefono',
		'password',
    ];

    public function citasComoPaciente()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    public function citasComoEspecialista()
    {
        return $this->hasMany(Cita::class, 'especialista_id');
    }
    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'user_id');
    }
	public function especialista()
	{
		return $this->hasOne(Especialista::class, 'user_id');
	}
}
