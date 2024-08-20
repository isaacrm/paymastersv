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
        Schema::create('estados_civiles', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD:database/migrations/2023_05_16_123015_create_estados_civiles_table.php
            $table->string('nombre', 150);
=======
            $table->string('nombre', 30);
            $table->string('descripcion', 100);
            $table->char('forma_aplicacion', 1);
            $table->char('obligatorio',1);
            $table->decimal('valor_porcentaje', 5, 4)->nullable();
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8:database/migrations/2023_05_15_184945_create_descuentos_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_civiles');
    }
};
