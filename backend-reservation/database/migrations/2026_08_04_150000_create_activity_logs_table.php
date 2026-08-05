<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('entreprise_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->string('module');

            $table->string('action');

            $table->text('description');

            $table->nullableMorphs('subject');

            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['entreprise_id', 'module', 'created_at']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
