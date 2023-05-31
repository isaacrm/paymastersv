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
            INSERT INTO registros (dias_trabajados, dias_ausente, dias_permiso, horas_trabajadas, horas_adicionales, horas_ausencia, empleados_id, planillas_id, created_at, updated_at)
            SELECT
                0,
                0,
                0,
                0,
                0,
                0,
                empleados.id,
                p_planilla_id,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            FROM empleados;

            -- Asignar ingresos a los registros
            INSERT INTO registro_ingresos (registros_id, ingresos_id, monto, created_at, updated_at)
            SELECT
                registros.id,
                ingresos.id,
                CASE
                    WHEN ingresos.forma_aplicacion IN ('P', 'T') AND ingresos.obligatorio = 'S' THEN 40 --temporal
                    WHEN ingresos.forma_aplicacion = 'M' AND ingresos.obligatorio = 'S' THEN 0
                    WHEN ingresos.forma_aplicacion = 'S' AND ingresos.obligatorio = 'S' THEN
                        CASE ingresos.campo_aplicar
                            WHEN 'dias_trabajados' THEN registros.dias_trabajados
                            WHEN 'dias_ausente' THEN registros.dias_ausente
                            WHEN 'dias_permiso' THEN registros.dias_permiso
                            WHEN 'horas_trabajadas' THEN registros.horas_trabajadas
                            WHEN 'horas_adicionales' THEN registros.horas_adicionales
                            WHEN 'horas_ausencia' THEN registros.horas_ausencia
                            ELSE 0
                        END
                    ELSE 0
                END,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            FROM ingresos, registros, empleados
            WHERE registros.planillas_id = p_planilla_id;

            -- Asignar descuentos a los registros
            INSERT INTO registro_descuentos (registros_id, descuentos_id, monto, created_at, updated_at)
            SELECT
                registros.id,
                descuentos.id,
                CASE
                    WHEN descuentos.forma_aplicacion IN ('P', 'T') AND descuentos.obligatorio = 'S' THEN 40 --temporal
                    WHEN descuentos.forma_aplicacion = 'M' AND descuentos.obligatorio = 'S' THEN 0
                    WHEN descuentos.forma_aplicacion = 'S' AND descuentos.obligatorio = 'S' THEN
                        CASE descuentos.campo_aplicar
                            WHEN 'dias_trabajados' THEN registros.dias_trabajados
                            WHEN 'dias_ausente' THEN registros.dias_ausente
                            WHEN 'dias_permiso' THEN registros.dias_permiso
                            WHEN 'horas_trabajadas' THEN registros.horas_trabajadas
                            WHEN 'horas_adicionales' THEN registros.horas_adicionales
                            WHEN 'horas_ausencia' THEN registros.horas_ausencia
                            ELSE 0
                        END
                    ELSE 0
                END,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            FROM descuentos, empleados, registros
            WHERE registros.planillas_id = p_planilla_id;

            -- Ingresando la información inicial en detalle_registros
            INSERT INTO detalle_registros (salario_base, total_ingresos, salario_total, total_descuentos, salario_liquido, registros_id, created_at, updated_at)
            SELECT
                empleados.salario_base,
                ingresos.total,
                (empleados.salario_base + ingresos.total),
                descuentos.total,
                (empleados.salario_base + ingresos.total - descuentos.total),
                registros.id,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            FROM empleados
            INNER JOIN registros ON empleados.id = registros.empleados_id
            LEFT JOIN (
                SELECT registros_id, COALESCE(SUM(monto),0) AS total
                FROM registro_ingresos
                GROUP BY registros_id
            ) ingresos ON registros.id = ingresos.registros_id
            LEFT JOIN (
                SELECT registros_id, COALESCE(SUM(monto),0) AS total
                FROM registro_descuentos
                GROUP BY registros_id
            ) descuentos ON registros.id = descuentos.registros_id
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
