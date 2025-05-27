<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Historial extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'paciente_id',
        'especialista_id',
        'comentarios_paciente',
        'observaciones_especialista',
        'recomendaciones',
        'dieta',
        'lista_compra',
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