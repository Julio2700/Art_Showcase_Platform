<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// --- 1. Import Controllers Auth yang Diperlukan ---
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;

// --- 2. Import Controllers Peran ---
use App\Http\Controllers\Admin\CategoryController; 
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Curator\ChallengeController;
use App\Http\Controllers\Curator\SubmissionController;
use App\Http\Controllers\Member\ArtworkController; 
use App\Http\Controllers\Member\InteractionController; 
use App\Http\Controllers\Member\SubmissionController as MemberSubmissionController; 
use App\Http\Controllers\Member\FavoriteController; 


// =========================================================
// A. GUEST ROUTES (Login, Register, Password & Verification)
// =========================================================

// --- Login & Register ---
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


// --- Password Reset ---
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');


// --- Email Verification (Diperlukan oleh Profile Settings) ---
Route::get('verify-email', EmailVerificationPromptController::class)
    ->middleware('auth')
    ->name('verification.notice');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store']) // <-- PERBAIKI SINTAKS
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// =========================================================
// B. AUTHENTICATED ROUTES (Role Specific)
// =========================================================

Route::middleware('auth')->group(function () {
    
    // --- Password Confirmation ---
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    
    
    // --- ROUTE KHUSUS MEMBER (Creative & Interaction) ---
    Route::middleware('role.member')->prefix('member')->name('member.')->group(function () {
        
        // Artwork Management (CRUD)
        Route::resource('artworks', ArtworkController::class);
        Route::resource('favorites', FavoriteController::class)->only(['index', 'destroy']);

        // Interaction Routes
        Route::post('interact/like/{artwork}', [InteractionController::class, 'toggleLike'])->name('interact.like');
        Route::post('interact/favorite/{artwork}', [InteractionController::class, 'toggleFavorite'])->name('interact.favorite');
        Route::post('interact/comment/{artwork}', [InteractionController::class, 'addComment'])->name('interact.comment');
        Route::post('interact/report/{artwork}', [InteractionController::class, 'reportContent'])->name('interact.report');
        Route::delete('interact/comment/{comment}', [InteractionController::class, 'deleteComment'])->name('interact.comment.delete');

        // Submission Challenge
        Route::get('submissions/create/{challenge}', [MemberSubmissionController::class, 'create'])->name('submissions.create');
        Route::post('submissions/store/{challenge}', [MemberSubmissionController::class, 'store'])->name('submissions.store');
    });


    // --- ROUTE KHUSUS ADMIN ---
    Route::middleware('role.admin')->prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard Admin (Target redirect dari HomeController)
        Route::get('dashboard', function () { return view('admin.dashboard.main'); })->name('dashboard'); 
        
        // User Management & Approval
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::put('users/{user}/approve', [UserController::class, 'updateApproval'])->name('users.approve');
        
        // Resources
        Route::resource('categories', CategoryController::class);
        Route::resource('reports', ReportController::class)->only(['index', 'show', 'update']);
    });


    // --- ROUTE KHUSUS CURATOR ---
    Route::middleware('curator.approved')->prefix('curator')->name('curator.')->group(function () {
        
        // Dashboard Curator
        Route::get('dashboard', function () { return view('curator.dashboard.main'); })->name('dashboard');
        
        // Challenge Management
        Route::resource('challenges', ChallengeController::class)->except(['show']);
        Route::get('challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');
        
        // Submission Management
        Route::get('challenges/{challenge}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
        Route::put('submissions/{submission}/set-winner', [SubmissionController::class, 'setWinner'])->name('submissions.set_winner');
    });

    // --- Lain-lain ---
    // Halaman Pending Approval (Aksesnya di luar group curator.approved)
    Route::get('curator/pending', function () {
        return view('curator.dashboard.pending');
    })->name('curator.pending');
    
});

// routes/auth.php (di dalam group Route Khusus CURATOR)

Route::middleware(['auth', 'curator.approved'])->prefix('curator')->name('curator.')->group(function () {
    
    // ... (rute challenges resource) ...
    
    // Route untuk menampilkan submissions list (curator.submissions.index)
    Route::get('challenges/{challenge}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    
    // 💡 SOLUSI: Tambahkan Route yang Hilang (curator.challenges.show_winners)
    Route::get('challenges/{challenge}/winners/select', [SubmissionController::class, 'showWinnersForm'])->name('challenges.show_winners');
    
    // Route untuk menyimpan pemenang (curator.submissions.set_winner)
    Route::put('submissions/{submission}/set-winner', [SubmissionController::class, 'setWinner'])->name('submissions.set_winner');

    // ... (rute lainnya)
});

// routes/auth.php (Di dalam group Route Khusus CURATOR)

Route::middleware(['auth', 'curator.approved'])->prefix('curator')->name('curator.')->group(function () {
    
    // ... routes yang sudah ada ...
    
    // Route untuk menampilkan form pemilihan pemenang (curator.challenges.show_winners)
    Route::get('challenges/{challenge}/winners/select', [SubmissionController::class, 'showWinnersForm'])->name('challenges.show_winners');
    
    // 💡 SOLUSI: TAMBAHKAN ROUTE POST UNTUK MEMPROSES FORM
    Route::post('challenges/{challenge}/winners', [SubmissionController::class, 'storeWinners'])->name('challenges.store_winners');
    
    // Route untuk menyimpan pemenang (curator.submissions.set_winner)
    Route::put('submissions/{submission}/set-winner', [SubmissionController::class, 'setWinner'])->name('submissions.set_winner');

    // ... rute lainnya ...
});