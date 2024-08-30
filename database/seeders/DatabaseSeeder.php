<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Bahasa;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BahasaSeeder::class,
            KategoriSeeder::class,
            // TingkatKesulitanSeeder::class,
        ]);
        User::create(['name' => 'Michael Isaiah', 'email' => 'michael.isaiah.02@gmail.com', 'password' => bcrypt('11111111')]);
    }
}
