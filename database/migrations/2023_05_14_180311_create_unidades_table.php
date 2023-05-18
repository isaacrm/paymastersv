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
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->unsignedBigInteger('superior_id')->nullable();
            $table->unsignedBigInteger('centro_costos_id')->nullable();

            $table->foreign('superior_id')
                ->references('superior_id')->on('puestos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('centro_costos_id')
                ->references('id')->on('centro_de_costos')
                ->onDelete('cascade')
                ->onUpdate('cascade');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
