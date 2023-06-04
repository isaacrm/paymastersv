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
                    WHEN ingresos.forma_aplicacion = 'M' THEN 0
                    WHEN ingresos.forma_aplicacion = 'F' THEN
                        CASE ingresos.id
                            WHEN 1 THEN registros.dias_trabajados * ((empleados.salario_base / 30) / 8) -- Horas extra
                            ELSE 0
                        END
                    ELSE 0
                END,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            FROM ingresos, registros, empleados
            WHERE registros.planillas_id = p_planilla_id AND ingresos.obligatorio = 'S' ;

            -- Asignar descuentos a los registros
            INSERT INTO registro_descuentos (registros_id, descuentos_id, monto, created_at, updated_at)
            SELECT
                registros.id,
                descuentos.id,
                CASE
                    WHEN descuentos.forma_aplicacion IN ('P', 'T') AND descuentos.nombre IN ('ISSS','AFP','ISR') THEN 0
                    WHEN descuentos.forma_aplicacion = 'M' THEN 0
                    WHEN descuentos.forma_aplicacion = 'F' THEN
                        CASE descuentos.id
                            WHEN 8 THEN registros.dias_ausente * (empleados.salario_base / 30)  -- Dias de ausencia
                            WHEN 9 THEN registros.horas_ausencia * ((empleados.salario_base / 30) / 8) -- Horas de asuencia
                            ELSE 0
                        END
                    ELSE 0
                END,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            FROM descuentos, empleados, registros
            WHERE registros.planillas_id = p_planilla_id AND descuentos.obligatorio = 'S';

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

            --Actualizando ISSS, AFP e ISR en registro_descuentos
            UPDATE registro_descuentos
            SET monto = (
                SELECT
                    CASE registro_descuentos.descuentos_id
                        WHEN 1 THEN descuentos.valor_porcentaje * detalle_registros.salario_total -- ISSS
                        WHEN 2 THEN descuentos.valor_porcentaje * detalle_registros.salario_total -- AFP
                        WHEN 3 THEN (
                        SELECT
                        ((detalle_registros.salario_total - (
                            SELECT COALESCE(SUM(registro_descuentos.monto), 0)
                            FROM registro_descuentos
                            INNER JOIN descuentos ON registro_descuentos.descuentos_id = descuentos.id
                            WHERE registro_descuentos.descuentos_id IN (1, 2)))
                             - rentas_mensuales.sobre_exceso) * rentas_mensuales.porcentaje_aplicar + rentas_mensuales.mas_fija
                        FROM detalle_registros, rentas_mensuales, registros
                        WHERE detalle_registros.salario_total BETWEEN rentas_mensuales.desde AND rentas_mensuales.hasta
                        AND registros.planillas_id = p_planilla_id
                        )
                    ELSE registro_descuentos.monto
                    END
                FROM descuentos
                INNER JOIN registros ON registro_descuentos.registros_id = registros.id
                INNER JOIN detalle_registros ON registros.id = detalle_registros.registros_id
                WHERE registro_descuentos.descuentos_id = descuentos.id
                AND registros.planillas_id = p_planilla_id
            )
            WHERE descuentos_id IN (1, 2, 3)
            AND registros_id IN (
                SELECT id
                FROM registros
                WHERE planillas_id = p_planilla_id
            );

            -- Actualizando detalle_registros
            UPDATE detalle_registros
            SET total_descuentos = (
                SELECT COALESCE(SUM(monto), 0)
                FROM registro_descuentos
                WHERE registros_id IN (
                    SELECT id
                    FROM registros
                    WHERE planillas_id = p_planilla_id
                )
            ),
            salario_liquido = salario_total - (
                SELECT COALESCE(SUM(monto), 0)
                FROM registro_descuentos
                WHERE registros_id IN (
                    SELECT id
                    FROM registros
                    WHERE planillas_id = p_planilla_id
                )
            )
            WHERE registros_id IN (
                SELECT id
                FROM registros
                WHERE planillas_id = p_planilla_id
            );
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
