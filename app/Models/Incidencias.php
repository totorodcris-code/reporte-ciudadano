<?php

namespace App\Models;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Comentarios;
use Illuminate\Database\Eloquent\Model;
use App\Models\Voto;

class Incidencias extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }
    public function votos()
    {
        return $this->hasMany(Voto::class, 'incidencia_id');
    }

    public function votosPositivos()
    {
        return $this->hasMany(Voto::class)->where('tipo', 'positivo');
    }

    public function votosNegativos()
    {
        return $this->hasMany(Voto::class)->where('tipo', 'negativo');
    }

    public function comentariosAprobados()
    {
        return $this->hasMany(Comentario::class);
    }
    protected $fillable = [
        'titulo', 'descripcion', 'imagen', 'latitud', 'longitud', 'direccion', 'estado', 'prioridad', 'user_id', 'categoria_id'
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    public function getImagenUrlAttribute()
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }
}
