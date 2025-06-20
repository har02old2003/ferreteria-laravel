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
        Schema::create('agile_sprints', function (Blueprint $table) {
            $table->id();
            $table->string('sprint_name', 100)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->integer('planned_points')->default(0);
            $table->integer('completed_points')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0); // Porcentaje de finalización
            $table->json('planned_stories')->nullable(); // Historias planeadas
            $table->text('sprint_goal')->nullable(); // Objetivo del sprint
            $table->text('retrospective_notes')->nullable(); // Notas de retrospectiva
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agile_sprints');
    }
};
