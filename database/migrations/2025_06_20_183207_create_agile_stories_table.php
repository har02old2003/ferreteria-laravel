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
        Schema::create('agile_stories', function (Blueprint $table) {
            $table->id();
            $table->string('story_id', 50)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('story_points')->default(0);
            $table->string('sprint_name', 100)->index();
            $table->string('assigned_to', 100)->index();
            $table->enum('status', ['todo', 'in_progress', 'in_review', 'completed', 'blocked'])->default('todo');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('epic', 100)->nullable(); // Épica a la que pertenece
            $table->json('acceptance_criteria')->nullable(); // Criterios de aceptación
            $table->json('metadata')->nullable(); // Metadatos adicionales
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agile_stories');
    }
};
