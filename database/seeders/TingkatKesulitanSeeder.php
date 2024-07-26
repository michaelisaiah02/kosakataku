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
            'bantuan_pengucapan' => true,
            'delay_bantuan' => 0,
            'maks_salah' => 10,
        ]);

        TingkatKesulitan::create([
            'tingkat_kesulitan' => 'normal',
            'bantuan_pengucapan' => true,
            'delay_bantuan' => 3,
            'maks_salah' => 5,
        ]);

        TingkatKesulitan::create([
            'tingkat_kesulitan' => 'sulit',
            'bantuan_pengucapan' => false,
            'delay_bantuan' => 0,
            'maks_salah' => 3,
        ]);
    }
}
