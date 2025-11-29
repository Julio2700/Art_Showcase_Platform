<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Import Controllers Publik
use App\Http\Controllers\Public\ArtworkCatalogController; 
use App\Http\Controllers\Public\ChallengeController; 
// Import Home Controller (Untuk dashboard setelah login)
use App\Http\Controllers\Member\HomeController; 


// 1. --- ROUTE AUTHENTIKASI (Bawaan Breeze) ---
// Ini mengimpor semua rute login, register, dan logout. 
// Pastikan file auth.php ADA dan berisi definisi route tersebut.
require __DIR__.'/auth.php';


// 2. --- ROUTE DASHBOARD (Setelah Login) ---
// Ganti route dashboard bawaan dengan HomeController yang menangani multi-role.
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// 3. --- ROUTE PROFIL (Bawaan Breeze) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// 4. --- ROUTE KHUSUS PUBLIC (Guest) ---

// Homepage Utama (Mengambil alih definisi Route::get('/') sebelumnya)
// Route::get('/', [ArtworkCatalogController::class, 'index'])->name('homepage'); // Route ini dijalankan
Route::get('/', [ArtworkCatalogController::class, 'index'])->name('homepage');


// Catalog Penuh (Jika Anda ingin halaman galeri terpisah dari homepage)
Route::get('/catalog', [ArtworkCatalogController::class, 'index'])->name('artworks.catalog');

// Product Details Page
Route::get('/artworks/{artwork}', [ArtworkCatalogController::class, 'show'])->name('artworks.show');

// Creator Profile Page
Route::get('/creator/{user}', [ArtworkCatalogController::class, 'showCreatorProfile'])->name('creator.profile');

// Challenge Detail Page
Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');