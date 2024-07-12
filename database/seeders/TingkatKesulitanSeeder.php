<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TingkatKesulitan;
use Illuminate\Support\Facades\DB;

class TingkatKesulitanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table first
        TingkatKesulitan::truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed the table with data
        TingkatKesulitan::create([
            'tingkat_kesulitan' => 'mudah',
            'bantuan_pengejaan' => true,
            'delay_bantuan' => 0,
            'maks_salah' => 9,
        ]);

        TingkatKesulitan::create([
            'tingkat_kesulitan' => 'normal',
            'bantuan_pengejaan' => true,
            'delay_bantuan' => 2,
            'maks_salah' => 4,
        ]);

        TingkatKesulitan::create([
            'tingkat_kesulitan' => 'sulit',
            'bantuan_pengejaan' => false,
            'delay_bantuan' => 0,
            'maks_salah' => 2,
        ]);
    }
}
