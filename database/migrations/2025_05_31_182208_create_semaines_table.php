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
        Schema::create('semaines', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_semaine');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->foreignId('annee_scolaire_id')
                ->constrained('annee_scolaires')
                ->onDelete('cascade');
            $table->foreignId('etablissement_id')
                ->constrained('etablissements')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semaines');
    }
};
