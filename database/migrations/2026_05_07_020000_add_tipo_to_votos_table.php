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
        Schema::table('votos', function (Blueprint $table) {
            // Add the 'tipo' column if it doesn't exist
            if (!Schema::hasColumn('votos', 'tipo')) {
                $table->enum('tipo', ['positivo', 'negativo'])->after('incidencia_id');
            }
            
            // Add indexes if they don't exist
            if (!Schema::hasIndex('votos', 'votos_incidencia_id_created_at_index')) {
                $table->index(['incidencia_id', 'created_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votos', function (Blueprint $table) {
            // Remove the 'tipo' column
            $table->dropColumn('tipo');
            
            // Remove the index
            $table->dropIndex(['incidencia_id', 'created_at']);
        });
    }
};
