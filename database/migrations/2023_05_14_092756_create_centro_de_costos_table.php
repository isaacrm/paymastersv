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
        Schema::create('centro_de_costos', function (Blueprint $table) {
            $table->id();
            $table->integer('mes_del');
            $table->integer('mes_al');
            $table->integer('anyo');
            $table->decimal('presupuesto_inicial', 9, 2);
            $table->decimal('presupuesto_restante', 9, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centro_de_costos');
    }
};
