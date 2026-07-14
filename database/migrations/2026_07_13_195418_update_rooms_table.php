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
        Schema::table('rooms', function (Blueprint $table) {

            // 1. Ajout de l'entreprise
            $table->foreignId('entreprise_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            // 2. Type de salle
            $table->enum('type', [
                'MEETING',
                'STORAGE'
            ])->after('entreprise_id');

            // 3. Suppression logique
            $table->softDeletes();

            // 4. Suppression de l'unicité actuelle
            $table->dropUnique('rooms_code_unique');

            // 5. Nouvelle contrainte d'unicité
            $table->unique(['entreprise_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            $table->dropUnique(['entreprise_id', 'code']);

            $table->unique('code');

            $table->dropSoftDeletes();

            $table->dropColumn('type');

            $table->dropConstrainedForeignId('entreprise_id');
        });
    }
};