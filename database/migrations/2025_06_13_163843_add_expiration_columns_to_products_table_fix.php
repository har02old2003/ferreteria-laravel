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
        Schema::table('products', function (Blueprint $table) {
            // Verificar si las columnas no existen antes de agregarlas
            if (!Schema::hasColumn('products', 'has_expiration')) {
                $table->boolean('has_expiration')->default(false)->comment('Indica si el producto tiene fecha de expiración');
            }
            
            if (!Schema::hasColumn('products', 'expiration_date')) {
                $table->date('expiration_date')->nullable()->comment('Fecha de expiración del producto');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'has_expiration')) {
                $table->dropColumn('has_expiration');
            }
            
            if (Schema::hasColumn('products', 'expiration_date')) {
                $table->dropColumn('expiration_date');
            }
        });
    }
};
