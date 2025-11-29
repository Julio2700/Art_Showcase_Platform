<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// --- GUEST ROUTES (Login/Register) ---

// Route untuk Menampilkan Form Login
Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login'); // WAJIB ADA

// Route untuk Memproses Form Login
Route::post('login', [AuthenticatedSessionController::class, 'store']);

// Route untuk Menampilkan Form Register
Route::get('register', [RegisteredUserController::class, 'create'])
    ->name('register'); // WAJIB ADA

// Route untuk Memproses Form Register
Route::post('register', [RegisteredUserController::class, 'store']);


// --- AUTHENTICATED ROUTES ---

// Route untuk Logout
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout'); 

// ... Tambahkan group Member, Curator, Admin di sini (seperti yang sudah kita bahas)
// require __DIR__.'/auth.php'; // TIDAK diperlukan di sini