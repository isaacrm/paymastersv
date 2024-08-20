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
        Schema::create('generos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
<<<<<<< HEAD:database/migrations/2023_05_15_202244_create_generos_table.php
=======
            $table->string('descripcion', 100);
            $table->char('forma_aplicacion', 1);
            $table->char('obligatorio',1);
            $table->decimal('valor_porcentaje', 5, 4)->nullable();
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:database/migrations/2023_05_14_211956_create_ingresos_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generos');
    }
};
