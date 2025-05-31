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
        Schema::create('sem_fers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ferie_id')->constrained('feries')->onDelete('cascade');
            $table->foreignId('semaine_id')->constrained('semaines')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sem_fers');
    }
};
