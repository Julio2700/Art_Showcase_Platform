<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// --- Import Controllers Peran ---
use App\Http\Controllers\Admin\CategoryController; 
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Curator\ChallengeController;
use App\Http\Controllers\Curator\SubmissionController;
use App\Http\Controllers\Member\ArtworkController; 
use App\Http\Controllers\Member\InteractionController; 
use App\Http\Controllers\Member\SubmissionController as MemberSubmissionController; 
use App\Http\Controllers\Member\FavoriteController; 
use App\Models\Comment; // Diperlukan untuk deleteComment

// --- 1. GUEST ROUTES (Login/Register/Logout) ---
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


// --- 2. Route Khusus MEMBER ---
Route::middleware(['auth', 'role.member'])->prefix('member')->name('member.')->group(function () {
    
    // Artwork Management (CRUD)
    Route::resource('artworks', ArtworkController::class);

    // Favorites Management (Index dan Delete)
    Route::resource('favorites', FavoriteController::class)->only(['index', 'destroy']);

    // Interaction Routes
    Route::post('interact/like/{artwork}', [InteractionController::class, 'toggleLike'])->name('interact.like');
    Route::post('interact/favorite/{artwork}', [InteractionController::class, 'toggleFavorite'])->name('interact.favorite');
    Route::post('interact/comment/{artwork}', [InteractionController::class, 'addComment'])->name('interact.comment');
    Route::post('interact/report/{artwork}', [InteractionController::class, 'reportContent'])->name('interact.report');
    Route::delete('interact/comment/{comment}', [InteractionController::class, 'deleteComment'])->name('interact.comment.delete'); // Tambahan Hapus Comment

    // Submission Challenge
    Route::get('submissions/create/{challenge}', [MemberSubmissionController::class, 'create'])->name('submissions.create');
    Route::post('submissions/store/{challenge}', [MemberSubmissionController::class, 'store'])->name('submissions.store');
});


// --- 3. Route Khusus ADMIN (Dibutuhkan oleh HomeController) ---
Route::middleware(['auth', 'role.admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // 💡 SOLUSI: MENDIFINISIKAN admin.dashboard
    Route::get('dashboard', function () {
        return view('admin.dashboard.main');
    })->name('dashboard'); // Route name is now 'admin.dashboard'

    Route::put('users/{user}/approve', [UserController::class, 'updateApproval'])->name('users.approve');
    
    // Resource Routes
    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
    Route::resource('reports', ReportController::class)->only(['index', 'show', 'update']);
});


// --- 4. Route Khusus CURATOR ---
Route::middleware(['auth', 'curator.approved'])->prefix('curator')->name('curator.')->group(function () {
    // CRUD Challenge (CREATE, STORE, INDEX, EDIT, UPDATE, DESTROY)
    Route::resource('challenges', ChallengeController::class)->except(['show']);
    // Route Show untuk Curator
    Route::get('challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');
    
    // Submission & Winner Management
    Route::get('challenges/{challenge}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::put('submissions/{submission}/set-winner', [SubmissionController::class, 'setWinner'])->name('submissions.set_winner');
});

// Halaman Pending Approval
Route::middleware('auth')->get('curator/pending', function () {
    return view('curator.dashboard.pending');
})->name('curator.pending');


Route::get('challenges/{challenge}/winners', [SubmissionController::class, 'showWinnersForm'])->name('challenges.show_winners');



