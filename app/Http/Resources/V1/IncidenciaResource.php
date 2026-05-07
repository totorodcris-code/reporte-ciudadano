<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'imagen' => $this->imagen ? url('/storage/' . $this->imagen) : null,
            'imagen_url' => $this->imagen ? url('/storage/' . $this->imagen) : null,
            'latitud' => (float) $this->latitud,
            'longitud' => (float) $this->longitud,
            'direccion' => $this->direccion,
            'estado' => $this->estado,
            'prioridad' => $this->prioridad,
            'fecha_reporte' => $this->fecha_reporte,
            'user_id' => $this->user_id,
            'categoria_id' => $this->categoria_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relaciones
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role
            ],
            
            'categoria' => [
                'id' => $this->categoria->id,
                'nombre_categoria' => $this->categoria->nombre_categoria,
                'descripcion' => $this->categoria->descripcion
            ],
            
            // Metadatos adicionales para mobile
            'resumen' => [
                'titulo_corto' => strlen($this->titulo) > 50 ? substr($this->titulo, 0, 47) . '...' : $this->titulo,
                'descripcion_corta' => strlen($this->descripcion) > 100 ? substr($this->descripcion, 0, 97) . '...' : $this->descripcion,
                'tiene_imagen' => !is_null($this->imagen),
                'estado_formateado' => $this->estado === 'resuelto' ? 'Resuelto' : 'Pendiente',
                'dias_desde_reporte' => $this->created_at->diffInDays(now())
            ]
        ];
    }
}
