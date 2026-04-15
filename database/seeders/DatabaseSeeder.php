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
        // 1. Run Master Data Seeder FIRST
        $this->call(MasterDataSeeder::class);

        // 2. Create System Users
        User::firstOrCreate([
            'email' => 'ranjit.warrgyizmorsch@gmail.com',
        ], [
            'name' => 'Ranjit',
            'password' => \Hash::make('aadi2003'),
            'role' => 'Super Admin',
        ]);

        User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Administrator',
            'password' => \Hash::make('password'),
            'role' => 'Super Admin',
        ]);
    }
}
