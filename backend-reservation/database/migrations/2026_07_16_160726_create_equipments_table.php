<?php

use App\Enums\Equipment\EquipmentUsageType;
use App\Enums\Equipment\EmpruntableEquipmentStatus;
use App\Enums\Equipment\NonEmpruntableEquipmentStatus;
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
        Schema::create('equipments', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('entreprise_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('equipment_categories')
                ->restrictOnDelete();

            $table->foreignId('storage_room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informations générales
            |--------------------------------------------------------------------------
            */

            $table->string('nom');

            $table->string('code');

            $table->string('marque');
               
            $table->string('modele');

            $table->string('localisation')
                ->nullable();

            $table->string('numero_serie')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Métier
            |--------------------------------------------------------------------------
            */

           $table->enum(
            'usage_type',
            EquipmentUsageType::values()
            );

            $table->string('etat');

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Contraintes
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'entreprise_id',
                'code',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('entreprise_id');
            $table->index('category_id');
            $table->index('storage_room_id');
            $table->index('assigned_to');
            $table->index('usage_type');
            $table->index('etat');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};