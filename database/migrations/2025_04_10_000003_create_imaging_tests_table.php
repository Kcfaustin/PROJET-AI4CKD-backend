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
        Schema::create('imaging_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->boolean('has_ultrasound')->default(false)->comment('A fait une u00e9chographie');
            $table->date('ultrasound_date')->nullable()->comment('Date de l\'u00e9chographie');
            $table->text('ultrasound_results')->nullable()->comment('Ru00e9sultats de l\'u00e9chographie');
            $table->boolean('has_ct_scan')->default(false)->comment('A fait un scanner');
            $table->date('ct_scan_date')->nullable()->comment('Date du scanner');
            $table->text('ct_scan_results')->nullable()->comment('Ru00e9sultats du scanner');
            $table->boolean('has_mri')->default(false)->comment('A fait une IRM');
            $table->date('mri_date')->nullable()->comment('Date de l\'IRM');
            $table->text('mri_results')->nullable()->comment('Ru00e9sultats de l\'IRM');
            $table->boolean('has_other_imaging')->default(false)->comment('A fait d\'autres examens d\'imagerie');
            $table->string('other_imaging_type')->nullable()->comment('Type d\'autre examen d\'imagerie');
            $table->date('other_imaging_date')->nullable()->comment('Date de l\'autre examen d\'imagerie');
            $table->text('other_imaging_results')->nullable()->comment('Ru00e9sultats de l\'autre examen d\'imagerie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imaging_tests');
    }
};
