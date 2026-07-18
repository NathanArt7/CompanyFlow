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
            'reservation_settings',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Relations
                |--------------------------------------------------------------------------
                */

                $table->foreignId('entreprise_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Configuration
                |--------------------------------------------------------------------------
                */

                $table->unsignedSmallInteger(
                    'reservation_buffer'
                )->default(0);

                /*
                |--------------------------------------------------------------------------
                | Dates
                |--------------------------------------------------------------------------
                */

                $table->timestamps();

            }
        );
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'reservation_settings'
        );
    }
};