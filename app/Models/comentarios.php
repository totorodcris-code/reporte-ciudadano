<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Incidencias;
use App\Models\Voto;
class comentarios extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function incidencia()
    {
        return $this->belongsTo(Incidencias::class);
    }
    public function votos()
    {
        return $this->hasMany(Voto::class);
    }
    protected $fillable = [
        'comentario', 'user_id', 'incidencia_id'
    ];
}
