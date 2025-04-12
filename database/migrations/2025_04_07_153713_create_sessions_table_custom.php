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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Utilisateur associé à la session
            $table->string('ip_address', 45)->nullable(); // Adresse IP
            $table->string('user_agent')->nullable(); // User agent (navigateur)
            $table->longText('payload'); // Données sérialisées de la session
            $table->integer('last_activity'); // Timestamp de la dernière activité
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
