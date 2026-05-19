<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DocumentoSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(MarcaSeeder::class);
        $this->call(PresentacionSeeder::class);
        $this->call(ProductoSeeder::class);
        $this->call(ClienteSeeder::class);
        $this->call(ComprobanteSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProveedorSeeder::class);
    }
}
