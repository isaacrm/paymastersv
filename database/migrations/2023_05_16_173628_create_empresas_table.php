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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
            $table->string('nit', 17);
            $table->string('telefono', 9);
            $table->string('nrc', 17);
            $table->string('email', 75);
            $table->string('sitio_web', 250);
            $table->string('numero_patronal', 50);
            $table->string('representante_legal', 250);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
