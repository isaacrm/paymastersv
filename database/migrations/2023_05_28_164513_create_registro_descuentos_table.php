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
        Schema::create('registro_descuentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registros_id')->constrained('registros');
            $table->foreignId('descuentos_id')->constrained('descuentos');
            $table->decimal('monto', 7, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_descuentos');
    }
};
