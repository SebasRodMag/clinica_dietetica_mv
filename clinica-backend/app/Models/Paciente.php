<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'numero_historial',
        'fecha_alta',
        'fecha_baja',
    ];

    protected $dates = ['fecha_alta', 'fecha_baja'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function historialMedico()
    {
        return $this->hasOne(HistorialMedico::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
