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
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->integer('dias_trabajados');
            $table->integer('horas_trabajadas');
            $table->integer('horas_adicionales');
            $table->integer('horas_ausencia');
            $table->foreignId('empleados_id')->constrained('empleados');
            $table->foreignId('planillas_id')->constrained('planillas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
