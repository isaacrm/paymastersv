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
        Schema::table('puestos', function (Blueprint $table) {
            $table->unsignedBigInteger('superior_id')->nullable();
            
            $table->foreign('superior_id')
                ->references('id')->on('puestos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     /*Schema::table('puestos', function (Blueprint $table) {
            $table->dropForeign(['puesto_superior_id']);
            $table->dropColumn('superior_id');
        });*/
    }
};
