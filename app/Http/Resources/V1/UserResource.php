<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'nombre' => $this->name,
            'email' => $this->email,
            'rol' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Metadatos para mobile
            'perfil' => [
                'iniciales' => strtoupper(substr($this->name, 0, 2)),
                'nombre_corto' => explode(' ', $this->name)[0],
                'es_admin' => $this->role === 'admin'
            ],
            
            // Estadísticas del usuario
            'estadisticas' => [
                'total_reportes' => $this->when(isset($this->reportes_count), $this->reportes_count),
                'reportes_resueltos' => $this->when(isset($this->resolved_reports_count), $this->resolved_reports_count),
                'reportes_pendientes' => $this->when(isset($this->pending_reports_count), $this->pending_reports_count)
            ]
        ];
    }
}
