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
        Schema::create('kidney_disease_stages', function (Blueprint $table) {
            $table->id();
            $table->integer('stage')->comment('0: Normal, 1-5: Stades de maladie rénale');
            $table->string('name');
            $table->text('description');
            $table->float('gfr_min')->comment('Débit de filtration glomérulaire minimum');
            $table->float('gfr_max')->comment('Débit de filtration glomérulaire maximum');
            $table->text('recommendations')->nullable()->comment('Recommandations médicales');
            $table->timestamps();

            // Optionnel : ajout d'un index pour le stage
            $table->index('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kidney_disease_stages');
    }
};
