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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->string("operacion", 1);
            $table->decimal("monto", 9, 2);
            $table->string("descripcion", 150);
            $table->unsignedBigInteger('centro_costos_id')->nullable();
            $table->unsignedBigInteger('planillas_id')->nullable();
            $table->timestamps();
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->foreign('centro_costos_id')
                ->references('id')->on('centro_de_costos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('planillas_id')
                ->references('id')->on('planillas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
