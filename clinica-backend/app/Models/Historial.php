<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Historial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paciente_id', 'especialista_id', 'descripcion'
    ];

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}