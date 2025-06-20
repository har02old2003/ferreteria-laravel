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
        Schema::create('system_failures', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index(); // Tipo de fallo
            $table->text('description'); // Descripción del fallo
            $table->json('context')->nullable(); // Contexto adicional
            $table->timestamp('occurred_at')->index(); // Cuándo ocurrió
            $table->string('severity', 20)->default('medium'); // Severidad: low, medium, high, critical
            $table->string('component', 100)->nullable(); // Componente afectado
            $table->string('user_id', 50)->nullable(); // Usuario afectado (si aplica)
            $table->string('ip_address', 45)->nullable(); // IP del usuario
            $table->boolean('resolved')->default(false); // Si fue resuelto
            $table->timestamp('resolved_at')->nullable(); // Cuándo fue resuelto
            $table->text('resolution_notes')->nullable(); // Notas de resolución
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_failures');
    }
};
