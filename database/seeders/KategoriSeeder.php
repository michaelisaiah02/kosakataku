<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table first
        Kategori::truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $json = File::get(resource_path('json/kategori.json'));
        $categories = json_decode($json, true)['kategori'];

        foreach ($categories as $category) {
            Kategori::create([
                'inggris' => $category['inggris'],
                'indonesia' => $category['indonesia']
            ]);
        }
    }
}
