<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    protected $fillable = [
        'user_id',
        'incidencia_id',
        'tipo'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incidencia(): BelongsTo
    {
        return $this->belongsTo(Incidencias::class);
    }

    public function scopePositivo($query)
    {
        return $query->where('tipo', 'positivo');
    }

    public function scopeNegativo($query)
    {
        return $query->where('tipo', 'negativo');
    }
}