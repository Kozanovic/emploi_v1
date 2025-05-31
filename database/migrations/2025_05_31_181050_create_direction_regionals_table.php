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
        Schema::create('direction_regionals', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->longText('adresse');
            $table->string('telephone');
            $table->foreignId('directeur_regional_id')
                ->constrained('directeur_regionals')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direction_regionals');
    }
};
