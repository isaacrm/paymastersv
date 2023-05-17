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
        Schema::create('techos_laborales', function (Blueprint $table) {
            $table->id();
            $table->decimal('afp',6,2);
            $table->decimal('isss',6,2);
            $table->integer('anyo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('techos_laborales');
    }
};
