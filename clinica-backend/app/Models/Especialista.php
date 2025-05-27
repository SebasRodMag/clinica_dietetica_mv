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
        'telefono',
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
}
