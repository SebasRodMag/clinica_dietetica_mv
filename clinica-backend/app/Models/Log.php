<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['user_id', 'accion', 'tabla_afectada', 'registro_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}