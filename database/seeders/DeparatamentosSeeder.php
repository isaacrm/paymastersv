<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeparatamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departamento::create(
            [
                "nombre" => "Ahuachapán",
                "codigo_iso" => "AH"
            ]
        );
        Departamento::create([
            "nombre" => "Cabañas",
            "codigo_iso" => "CA"
        ],);
        Departamento::create([
            "nombre" => "Chalatenango",
            "codigo_iso" => "CH"
        ],);
        Departamento::create([
            "nombre" => "Cuscatlán",
            "codigo_iso" => "CU"
        ],);
        Departamento::create([
            "nombre" => "La Libertad",
            "codigo_iso" => "LL"
        ],);
        Departamento::create([
            "nombre" => "La Paz",
            "codigo_iso" => "PA"
        ],);
        Departamento::create([
            "nombre" => "La Unión",
            "codigo_iso" => "UN"
        ],);
        Departamento::create([
            "nombre" => "Morazán",
            "codigo_iso" => "MO"
        ],);
        Departamento::create([
            "nombre" => "San Miguel",
            "codigo_iso" => "SM"
        ],);
        Departamento::create([
            "nombre" => "San Salvador",
            "codigo_iso" => "SS"
        ],);
        Departamento::create([
            "nombre" => "San Vicente",
            "codigo_iso" => "SV"
        ],);
        Departamento::create([
            "nombre" => "Santa Ana",
            "codigo_iso" => "SA"
        ],);
        Departamento::create([
            "nombre" => "Sonsonate",
            "codigo_iso" => "SO"
        ],);
        Departamento::create([
            "nombre" => "Usulután",
            "codigo_iso" => "US"
        ]);
    }
}
