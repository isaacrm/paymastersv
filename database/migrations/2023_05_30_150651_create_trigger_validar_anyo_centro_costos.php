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
            CREATE TRIGGER validar_anyo
            BEFORE INSERT OR UPDATE ON centro_costos
            FOR EACH ROW
            BEGIN
                IF :NEW.anyo < 1900 OR :NEW.anyo > 2100 THEN
                    RAISE_APPLICATION_ERROR(-20001, \'El año debe estar entre 1900 y 2100\');
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER validar_anyo');
    }
};
