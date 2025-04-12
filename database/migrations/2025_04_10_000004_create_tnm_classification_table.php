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
        Schema::create('tnm_classification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('t_stage')->comment('T - Tumeur primaire (T1, T2, T3, T4)');
            $table->string('n_stage')->comment('N - Ganglions lymphatiques ru00e9gionaux (N0, N1, N2, N3)');
            $table->string('m_stage')->comment('M - Mu00e9tastases u00e0 distance (M0, M1)');
            $table->string('overall_stage')->comment('Stade global (I, II, III, IV)');
            $table->string('grade')->comment('Grade histologique (G1, G2, G3, G4)');
            $table->text('notes')->nullable()->comment('Notes additionnelles');
            $table->date('classification_date')->comment('Date de la classification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tnm_classification');
    }
};
