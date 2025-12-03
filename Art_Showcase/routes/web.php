<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Public\ArtworkCatalogController; 
use App\Http\Controllers\Public\ChallengeController; 
use App\Http\Controllers\Member\HomeController; 


// 1. --- ROUTE AUTHENTIKASI ---
require __DIR__.'/auth.php';


// 2. --- ROUTE DASHBOARD (Setelah Login) ---
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// 3. --- ROUTE PROFIL ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// 4. --- ROUTE KHUSUS PUBLIC (Guest) ---

// Homepage Utama
Route::get('/', [ArtworkCatalogController::class, 'index'])->name('homepage');


// Catalog Penuh 
Route::get('/catalog', [ArtworkCatalogController::class, 'index'])->name('artworks.catalog');

// Product Details Page
Route::get('/artworks/{artwork}', [ArtworkCatalogController::class, 'show'])->name('artworks.show');

// Creator Profile Page
Route::get('/creator/{user}', [ArtworkCatalogController::class, 'showCreatorProfile'])->name('creator.profile');

// Challenge Detail Page (Publik)
Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');

// 💡 BLOK ADMIN DAN CURATOR TELAH DIHAPUS DARI SINI DAN DIPINDAHKAN KE auth.php


### 2. File Perbaikan: `routes/auth.php` (Mendefinisikan `admin.dashboard`)

Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

