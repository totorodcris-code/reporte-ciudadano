<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void //subir
    {
        Schema::create('votos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha')->useCurrent();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('incidencia_id')->constrained('incidencias')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'incidencia_id']);// Evita que un usuario vote más de una vez por la misma incidencia
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void //eliminar o reescribir
    {
        Schema::dropIfExists('votos');
    }
};
