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
        // migration
        Schema::table('seances', function (Blueprint $table) {
            $table->foreignId('supprime_par_ferie_id')->nullable()->constrained('feries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seances', function (Blueprint $table) {
            //
        });
    }
};
