<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER centro_de_costos_validar_presupuesto_restante
            BEFORE INSERT OR UPDATE ON centro_de_costos
            FOR EACH ROW
            BEGIN
                IF :NEW.presupuesto_restante < 0 THEN
                    RAISE_APPLICATION_ERROR(-20001, \'El presupuesto restante debe ser mayor o igual a 0\');
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //DB::unprepared('DROP TRIGGER centro_de_costos_validar_presupuesto_restante');
    }
};
