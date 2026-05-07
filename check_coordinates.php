<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN DE COORDENADAS ===\n";
$incidencias = \App\Models\Incidencias::all();

foreach ($incidencias as $incidencia) {
    echo "Reporte #{$incidencia->id} - {$incidencia->titulo}\n";
    echo "  Latitud: " . ($incidencia->latitud ?? 'NULL') . "\n";
    echo "  Longitud: " . ($incidencia->longitud ?? 'NULL') . "\n";
    echo "  Dirección: " . ($incidencia->direccion ?? 'NULL') . "\n";
    echo "  ¿Tiene coordenadas?: " . (($incidencia->latitud && $incidencia->longitud) ? 'SÍ' : 'NO') . "\n";
    echo "\n";
}

echo "=== RESUMEN ===\n";
$conCoordenadas = \App\Models\Incidencias::whereNotNull('latitud')
    ->whereNotNull('longitud')
    ->where('latitud', '!=', '')
    ->where('longitud', '!=', '')
    ->count();

echo "Incidencias con coordenadas: {$conCoordenadas} de " . \App\Models\Incidencias::count() . "\n";

if ($conCoordenadas === 0) {
    echo "\n⚠️  PROBLEMA: Ninguna incidencia tiene coordenadas válidas\n";
    echo "El mapa no aparecerá porque no hay datos geográficos que mostrar.\n";
    echo "\nSOLUCIÓN:\n";
    echo "1. Crear incidencias con coordenadas válidas\n";
    echo "2. O actualizar las incidencias existentes con latitud/longitud\n";
}
