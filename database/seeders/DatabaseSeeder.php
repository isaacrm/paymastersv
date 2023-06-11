<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(PermisosSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UsuariosSeeder::class);
        $this->call(GenerosSeeder::class);
        $this->call(EstadosCivilesSeeder::class);
        $this->call(DeparatamentosSeeder::class);
        $this->call(MunicipiosSeeder::class);
        $this->call(OcupacionesSeeder::class);
        $this->call(TipoDocumentosSeeder::class);
        $this->call(PuestosSeeder::class);
        $this->call(DireccionesSeeder::class);
        $this->call(DescuentoSeeder::class);
        $this->call(IngresoSeeder::class);
        $this->call(RentaSeeder::class);
    }
}
