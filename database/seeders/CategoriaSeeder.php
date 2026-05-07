<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::create([
            'nombre_categoria' => 'Infraestructura',
            'descripcion' => 'Baches, banquetas, guarniciones y daños en la vía pública'
        ]);

        Categoria::create([
            'nombre_categoria' => 'Alumbrado Público',
            'descripcion' => 'Farolas fundidas, postes dañados o sin funcionamiento'
        ]);

        Categoria::create([
            'nombre_categoria' => 'Limpieza',
            'descripcion' => 'Recolección de basura, limpieza de calles y áreas públicas'
        ]);

        Categoria::create([
            'nombre_categoria' => 'Seguridad',
            'descripcion' => 'Situaciones de riesgo, vandalismo o actividades sospechosas'
        ]);

        Categoria::create([
            'nombre_categoria' => 'Agua Potable',
            'descripcion' => 'Fugas, tuberías rotas o problemas con el suministro de agua'
        ]);

        Categoria::create([
            'nombre_categoria' => 'Áreas Verdes',
            'descripcion' => 'Parques, jardines, poda de árboles y mantenimiento de zonas verdes'
        ]);
    }
}