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
        Schema::create('ocupaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
<<<<<<< HEAD:database/migrations/2023_05_16_154800_create_ocupaciones_table.php
=======
            $table->integer('mes_del');
            $table->integer('mes_al');
            $table->integer('anyo');
            $table->decimal('presupuesto_inicial', 9, 2);
            $table->decimal('presupuesto_restante', 9, 2);
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:database/migrations/2023_05_14_092756_create_centro_de_costos_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocupaciones');
    }
};
