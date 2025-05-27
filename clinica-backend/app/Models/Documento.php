<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'historial_id',
        'user_id',
        'nombre',
        'archivo',
        'tipo',
        'tamano',
        'descripcion',
    ];

    // Relación con el historial
    public function historial()
    {
        return $this->belongsTo(Historial::class);
    }

    // Relación con el usuario (propietario)
    public function propietario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
