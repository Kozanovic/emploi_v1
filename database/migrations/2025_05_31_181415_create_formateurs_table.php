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
        Schema::create('formateurs', function (Blueprint $table) {
            $table->id();
            $table->string('specialite');
            $table->boolean('peut_gerer_seance')->default(false);
            $table->foreignId('utilisateur_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('etablissement_id')
                ->constrained('etablissements')
                ->onDelete('cascade');
            $table->foreignId('complexe_id')
                ->constrained('complexes')
                ->onDelete('cascade');
            $table->foreignId('direction_regional_id')
                ->constrained('direction_regionals')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formateurs');
    }
};
