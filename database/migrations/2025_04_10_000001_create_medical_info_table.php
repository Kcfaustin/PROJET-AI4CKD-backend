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
        Schema::create('medical_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->comment('Médecin responsable')->constrained()->onDelete('restrict');
            $table->foreignId('kidney_disease_stage_id')->nullable()->constrained()->onDelete('set null');
            $table->date('diagnosis_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('on_dialysis')->default(false);
            $table->date('dialysis_start_date')->nullable();
            $table->text('current_treatment')->nullable();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->float('creatinine_level')->nullable()->comment('Niveau de créatinine');
            $table->float('gfr')->nullable()->comment('Débit de filtration glomérulaire');
            $table->float('albuminuria')->nullable()->comment('Albuminurie');
            $table->integer('blood_pressure_systolic')->nullable()->comment('Tension artérielle systolique');
            $table->integer('blood_pressure_diastolic')->nullable()->comment('Tension artérielle diastolique');
            $table->float('potassium_level')->nullable()->comment('Niveau de potassium');
            $table->float('hemoglobin_level')->nullable()->comment('Niveau d\'hémoglobine');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_info');
    }
};
