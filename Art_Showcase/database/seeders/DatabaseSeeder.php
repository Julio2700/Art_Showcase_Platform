<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 💡 AKUN ADMIN (Wajib)
        User::create([
            'name' => 'Admin Super',
            'display_name' => 'Admin Super',
            'email' => 'adminsuper@gmail.com',
            'password' => Hash::make('12345678'), 
            'role' => 'admin', // Role Admin
            'is_approved' => true, // Disetujui
        ]);

        // 💡 PANGGIL CATEGORY SEEDER
        $this->call(CategorySeeder::class);
    }

    
}
