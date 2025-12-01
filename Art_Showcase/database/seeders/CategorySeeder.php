<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Digital Art'],
            ['name' => 'Illustration'],
            ['name' => 'Photography'],
            ['name' => 'UI/UX Design'], // Kategori sesuai dokumen
            ['name' => '3D Art'],      // Kategori sesuai dokumen
            ['name' => 'Vector Art'],
            ['name' => 'Drawing'],
        ];

        DB::table('categories')->insert($categories);
    }
}