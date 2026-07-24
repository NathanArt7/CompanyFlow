<?php

use App\Enums\Reservation\DayOfWeek;
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
        Schema::create('service_hours', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('reservation_setting_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Configuration du jour
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'day_of_week',
                DayOfWeek::values()
            );

            $table->boolean('is_open')
                ->default(true);

            $table->time('start_time')
                ->nullable();

            $table->time('end_time')
                ->nullable();

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
                'reservation_setting_id',
                'day_of_week',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('day_of_week');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'service_hours'
        );
    }
};