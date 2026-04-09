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
        // Admin User
        \App\Models\User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@ppdb.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Default Jurusan
        \App\Models\Jurusan::create(['nama' => 'Rekayasa Perangkat Lunak', 'kuota' => 100]);
        \App\Models\Jurusan::create(['nama' => 'Teknik Komputer dan Jaringan', 'kuota' => 100]);
        \App\Models\Jurusan::create(['nama' => 'Multimedia', 'kuota' => 80]);

        // Default Pengaturan
        \App\Models\Pengaturan::create(['key' => 'bobot_rapor', 'value' => '40']);
        \App\Models\Pengaturan::create(['key' => 'bobot_ujian', 'value' => '60']);
        \App\Models\Pengaturan::create(['key' => 'tanggal_pengumuman', 'value' => '2024-07-01']);
    }
}
