<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    $this->call(RolesSeeder::class);
    $this->call(UserSeeder::class);
    $this->call(EspecialistaSeeder::class);
    $this->call(PacienteSeeder::class);
    $this->call(CitasSeeder::class);
    }
}
