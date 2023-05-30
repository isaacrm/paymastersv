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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('apellido_paterno', 30);
            $table->string('apellido_materno', 30);
            $table->string('apellido_casada', 35);
            $table->string('primer_nombre', 25);
            $table->string('segundo_nombre', 25);
            $table->date('fecha_nacimiento');
            $table->string('identificacion', 25);
            $table->string('nit', 17)->unique();
            $table->string('isss', 25)->unique();
            $table->string('nup', 20)->unique();
            $table->string('email_personal', 175)->unique();
            $table->date('fecha_ingreso');
            $table->string('email_profesional', 175)->unique()->nullable();
            $table->decimal('salario_base', 8, 2);

            // * Las foraneas
            $table->unsignedBigInteger('estados_civiles_id');
            $table->unsignedBigInteger('generos_id');
            $table->unsignedBigInteger('ocupaciones_id');
            $table->unsignedBigInteger('tipo_documentos_id')->nullable();
            $table->unsignedBigInteger('direcciones_id');
            $table->unsignedBigInteger('puestos_id');

            $table->timestamps();
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->foreign('estados_civiles_id')
                ->references('id')->on('estados__civiles')
                ->onUpdate('cascade');
            $table->foreign('generos_id')
                ->references('id')->on('generos')
                ->onUpdate('cascade');
            $table->foreign('tipo_documentos_id')
                ->references('id')->on('tipo_documentos')
                ->onUpdate('cascade');
            $table->foreign('direcciones_id')
                ->references('id')->on('direccions')
                ->onUpdate('cascade');
            $table->foreign('puestos_id')
                ->references('id')->on('puestos')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
