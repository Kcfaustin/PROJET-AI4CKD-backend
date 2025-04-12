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
        Schema::create('medical_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->boolean('diabetes')->default(false)->comment('Diabu00e8te');
            $table->boolean('hypertension')->default(false)->comment('Hypertension');
            $table->boolean('heart_disease')->default(false)->comment('Maladie cardiaque');
            $table->boolean('liver_disease')->default(false)->comment('Maladie du foie');
            $table->boolean('autoimmune_disease')->default(false)->comment('Maladie auto-immune');
            $table->enum('smoking_status', ['non-fumeur', 'occasionnel', 'ru00e9gulier'])->default('non-fumeur');
            $table->enum('bmi_status', ['sous-poids', 'normal', 'surpoids', 'obu00e8se'])->default('normal');
            $table->enum('alcohol_consumption', ['occasionnel', 'modu00e9ru00e9', 'u00e9levu00e9'])->default('occasionnel');
            $table->boolean('sedentary')->default(false)->comment('Mode de vie su00e9dentaire');
            $table->text('other_factors')->nullable()->comment('Autres facteurs de risque');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_history');
    }
};
