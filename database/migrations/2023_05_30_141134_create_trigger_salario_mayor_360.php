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
        DB::unprepared("
        CREATE OR REPLACE TRIGGER puestos_salario_desde_mayor_360
        BEFORE INSERT OR UPDATE ON puestos
        FOR EACH ROW
        BEGIN
            IF :NEW.salario_desde <= 360 THEN
                RAISE_APPLICATION_ERROR(-20001, 'El salario mínimo debe ser mayor a $360');
            END IF;
        END;
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //DB::unprepared('DROP TRIGGER salario_desde_mayor_360');
    }
};
