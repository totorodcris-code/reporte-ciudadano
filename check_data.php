<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USUARIOS ===\n";
$users = \App\Models\User::select('id', 'name', 'email')->get();
foreach ($users as $user) {
    echo "ID: {$user->id} - {$user->name} ({$user->email})\n";
}

echo "\n=== INCIDENCIAS ===\n";
$incidencias = \App\Models\Incidencias::with('user')->get();
foreach ($incidencias as $incidencia) {
    $userName = $incidencia->user ? $incidencia->user->name : 'Sin usuario';
    echo "Reporte #{$incidencia->id} - Usuario ID: {$incidencia->user_id} - {$userName}\n";
    echo "  Título: {$incidencia->titulo}\n";
    echo "  Estado: {$incidencia->estado}\n";
    echo "  Creado: {$incidencia->created_at}\n\n";
}

echo "=== RESUMEN ===\n";
echo "Total usuarios: " . \App\Models\User::count() . "\n";
echo "Total incidencias: " . \App\Models\Incidencias::count() . "\n";

// Verificar incidencias por usuario
echo "\nIncidencias por usuario:\n";
$incidenciasPorUsuario = \App\Models\Incidencias::select('user_id', \DB::raw('count(*) as count'))
    ->groupBy('user_id')
    ->get();
    
foreach ($incidenciasPorUsuario as $item) {
    echo "Usuario ID {$item->user_id}: {$item->count} incidencias\n";
}
