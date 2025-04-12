<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['Mensuel', 'Trimestriel', 'Annuel', 'Personnalisé']);
            $table->enum('category', ['Patients', 'Traitements', 'Médicaments', 'Performance']);
            $table->string('date', 10); // Format DD/MM/YYYY
            $table->string('author', 100);
            $table->enum('status', ['Généré', 'En cours', 'Programmé']);
            $table->enum('format', ['PDF', 'Excel', 'CSV']);
            $table->timestamps(); // created_at & updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
