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
        CREATE OR REPLACE TRIGGER empleados_check_email_personal
        BEFORE INSERT OR UPDATE ON empleados
        FOR EACH ROW
        DECLARE
            email_regex VARCHAR2(100) := '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$';
        BEGIN
        IF :NEW.email_personal IS NOT NULL AND NOT REGEXP_LIKE(:NEW.email_personal, email_regex) THEN
                RAISE_APPLICATION_ERROR(-20001, 'El email personal no es válido');
            END IF;
        END;
        ");

        DB::unprepared("
        CREATE OR REPLACE TRIGGER empleados_check_email_profesional
        BEFORE INSERT OR UPDATE ON empleados
        FOR EACH ROW
        DECLARE
            email_regex VARCHAR2(100) := '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$';
        BEGIN
        IF :NEW.email_profesional IS NOT NULL AND NOT REGEXP_LIKE(:NEW.email_profesional, email_regex) THEN
                RAISE_APPLICATION_ERROR(-20001, 'El email profesional no es válido');
            END IF;
        END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //DB::unprepared('DROP TRIGGER empleados_check_email_personal;');
        //DB::unprepared('DROP TRIGGER empleados_check_email_profesional;');
    }
};
