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
        Schema::create('registro_ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registros_id')->constrained('registros');
            $table->foreignId('ingresos_id')->constrained('ingresos');
            $table->decimal('monto', 7, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_ingresos');
    }
};
