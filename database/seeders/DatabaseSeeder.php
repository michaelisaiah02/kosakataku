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
        $jsonLanguage = resource_path('json/bahasa.json');
        $languages = json_decode(file_get_contents($jsonLanguage), true);

        foreach ($languages as $language) {
            Bahasa::create([
                'bahasa' => $language['language'],
                'deeplcode' => $language['deeplcode'],
                'googlecode' => $language['googlecode'],
            ]);
        }

    }
}
