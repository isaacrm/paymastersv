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
        Schema::create('direccions', function (Blueprint $table) {
            $table->id();
            $table->string('calle',30);
            $table->string('colonia',30);
            $table->string('identificador_casa',10);
            $table->integer('apto_local');
            $table->unsignedBigInteger('municipio_id');
            $table->timestamps();

            $table->foreign('municipio_id')->references('id')->on('municipios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direccions');
    }
};
