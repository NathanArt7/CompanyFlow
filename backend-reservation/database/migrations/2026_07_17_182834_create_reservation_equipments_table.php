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
        Schema::create(
            'reservation_equipments',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relations
                |--------------------------------------------------------------------------
                */

                $table->foreignId('reservation_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('equipment_id')
                    ->constrained('equipments');

                /*
                |--------------------------------------------------------------------------
                | Dates
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Contraintes
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'reservation_id',
                    'equipment_id',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Index
                |--------------------------------------------------------------------------
                */

                $table->index('reservation_id');

                $table->index('equipment_id');

            }
        );
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'reservation_equipments'
        );
    }
};