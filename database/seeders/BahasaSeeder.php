<?php

namespace Database\Seeders;

use App\Models\Bahasa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BahasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table first
        Bahasa::truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Load JSON data
        $json = File::get(resource_path('json/bahasa.json'));
        $languages = json_decode($json, true)['bahasa'];

        // Insert each language into the database
        foreach ($languages as $language) {
            Bahasa::create([
                'inggris' => $language['inggris'],
                'indonesia' => $language['indonesia'],
                'kode_tts' => $language['kode_tts'],
                'kode_stt' => $language['kode_stt'],
                'suara_pria' => $language['suara_pria'],
                'suara_wanita' => $language['suara_wanita'],
            ]);
        }
    }
}
