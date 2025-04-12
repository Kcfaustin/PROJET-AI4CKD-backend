<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('user_id')->comment('Médecin')->constrained('users')->onDelete('restrict');
            $table->date('date'); // Restaurer date
            $table->time('time'); // Restaurer time
            $table->string('duration');
            $table->string('type');
            $table->enum('status', ['Planifié', 'Confirmé', 'Annulé', 'Terminé'])->default('Planifié');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ajout des index pour optimisation (facultatif)
            $table->index(['patient_id', 'user_id']);
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
