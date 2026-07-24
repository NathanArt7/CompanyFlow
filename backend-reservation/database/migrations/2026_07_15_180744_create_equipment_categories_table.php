<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();

            // Entreprise propriétaire de la catégorie
            $table->foreignId('entreprise_id')
                ->constrained()
                ->cascadeOnDelete();

            // Informations
            $table->string('nom');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Contraintes
            $table->unique([
                'entreprise_id',
                'nom',
            ]);

            
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_categories');
    }
};