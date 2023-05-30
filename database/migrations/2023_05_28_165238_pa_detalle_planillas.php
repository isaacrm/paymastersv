<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
        CREATE OR REPLACE PROCEDURE pa_detalle_planillas(p_planilla_id IN NUMBER) AS
        BEGIN
            -- Crear registros para empleados activos
            INSERT INTO registros (dias_trabajados, horas_trabajadas, horas_adicionales, horas_ausencia, empleados_id, planillas_id, created_at, updated_at)
            SELECT 0,0,0,0, empleados.id, p_planilla_id, SYSTIMESTAMP, SYSTIMESTAMP
            FROM empleados;

            -- Asignar ingresos a los registros
            INSERT INTO registro_ingresos (registros_id, ingresos_id, monto, created_at, updated_at)
            SELECT registros.id, ingresos.id, 0, SYSTIMESTAMP, SYSTIMESTAMP
            FROM ingresos, registros
            WHERE registros.planillas_id = p_planilla_id;

            -- Asignar descuentos a los registros
            INSERT INTO registro_descuentos (registros_id, descuentos_id, monto, created_at, updated_at)
            SELECT registros.id, descuentos.id, empleados.salario_base, SYSTIMESTAMP, SYSTIMESTAMP
            FROM descuentos, empleados, registros
            WHERE registros.planillas_id = p_planilla_id;
        END
    ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE pa_detalle_registros");
    }
};
