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
        Schema::create('rentas_mensuales', function (Blueprint $table) {
            $table->id();
            $table->integer('tramo')->unique();
            $table->decimal('desde',6,2);
            $table->decimal('hasta',6,2);
            $table->decimal('porcentaje_aplicar',5,4);
            $table->decimal('sobre_exceso',6,2);
            $table->decimal('mas_fija',6,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentas_mensuales');
    }
};
