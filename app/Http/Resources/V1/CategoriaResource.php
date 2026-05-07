<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
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
            'nombre' => $this->nombre_categoria,
            'descripcion' => $this->descripcion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Metadatos para mobile
            'estadisticas' => [
                'total_incidencias' => $this->when(isset($this->incidencias_count), $this->incidencias_count),
                'incidencias_resueltas' => $this->when(isset($this->resolved_incidencias_count), $this->resolved_incidencias_count),
                'incidencias_pendientes' => $this->when(isset($this->pending_incidencias_count), $this->pending_incidencias_count)
            ],
            
            'ui_config' => [
                'color' => $this->getColorForCategory(),
                'icono' => $this->getIconForCategory(),
                'activo' => true
            ]
        ];
    }
    
    private function getColorForCategory(): string
    {
        $colors = [
            'Infraestructura' => '#3B82F6',
            'Alumbrado Público' => '#F59E0B',
            'Limpieza' => '#10B981',
            'Agua Potable' => '#06B6D4',
            'Áreas Verdes' => '#84CC16'
        ];
        
        return $colors[$this->nombre_categoria] ?? '#6B7280';
    }
    
    private function getIconForCategory(): string
    {
        $icons = [
            'Infraestructura' => 'fa-hammer',
            'Alumbrado Público' => 'fa-lightbulb',
            'Limpieza' => 'fa-broom',
            'Agua Potable' => 'fa-tint',
            'Áreas Verdes' => 'fa-tree'
        ];
        
        return $icons[$this->nombre_categoria] ?? 'fa-exclamation-triangle';
    }
}
