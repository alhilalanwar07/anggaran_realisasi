<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Realisasi;
use Illuminate\Database\Seeder;
use App\Models\Anggaran;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Realisasi::whereNull('anggaran_id')->delete();
        // Delete records from realisasis table where tahun is 2022
        // Realisasi::where('tahun', 2021)->delete();

        // Delete records from anggarans table where tahun is 2021
        // Anggaran::where('tahun', 2021)->delete();

        // kosongkan semua tabel

    }
}
