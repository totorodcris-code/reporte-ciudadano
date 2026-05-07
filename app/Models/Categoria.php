<?php

namespace App\Models;
use App\Models\Incidencias;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
   public function incidencias()
    {
        return $this->hasMany(Incidencias::class);
    }
    protected $fillable = [
        'nombre_categoria', 'descripcion'
    ];
}
