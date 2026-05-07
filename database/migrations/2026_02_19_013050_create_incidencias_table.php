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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('imagen') ->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->string('direccion');
            $table->timestamp('fecha_reporte')->useCurrent();
            $table->string('estado')->default('pendiente');
            $table->integer('prioridad')->default(1);
            $table->foreignId('user_id')->constrained()->onDelete('cascade')-> references('id')->on('users');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade') -> references('id')->on('categorias');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
