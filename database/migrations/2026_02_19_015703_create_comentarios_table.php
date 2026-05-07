<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
            $table->timestamp('fecha')->useCurrent();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')-> references('id')->on('users');
            $table->foreignId('incidencia_id')->constrained('incidencias')->onDelete('cascade') -> references('id')->on('incidencias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
