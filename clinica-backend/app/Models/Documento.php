<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = ['paciente_id', 'especialista_id', 'nombre', 'ruta', 'descripcion'];

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }

    public function historial()
    {
        return $this->belongsTo(Historial::class);
    }
	
	public function paciente()
	{
		return $this->historial->paciente;
	}
	
	
}
