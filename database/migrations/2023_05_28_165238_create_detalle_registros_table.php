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
        Schema::create('detalle_registros', function (Blueprint $table) {
            $table->id();
            $table->decimal('salario_base',7,2);
            $table->decimal('total_ingresos',7,2);
            $table->decimal('salario_total',7,2);
            $table->decimal('total_descuentos',7,2);
            $table->decimal('salario_liquido',7,2);
            $table->foreignId('registros_id')->constrained('registros');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_registros');
    }
};
