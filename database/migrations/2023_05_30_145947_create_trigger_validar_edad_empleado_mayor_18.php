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
            CREATE TRIGGER edad_empleado_mayor_18
            BEFORE INSERT OR UPDATE ON empleados
            FOR EACH ROW
            BEGIN
                IF EXTRACT(YEAR FROM SYSDATE) - EXTRACT(YEAR FROM :NEW.fecha_nacimiento) < 18 THEN
                    RAISE_APPLICATION_ERROR(-20001, \'La edad debe ser mayor o igual a 18 años\');
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER edad_empleado_mayor_18');
    }
};
