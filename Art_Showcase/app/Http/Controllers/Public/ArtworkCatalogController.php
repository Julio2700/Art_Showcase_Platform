<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; // Perlu untuk cek status Like/Favorite

class ArtworkCatalogController extends Controller
{
    /**
     * Menampilkan Galeri Karya Utama (Homepage / Catalog)
     */
    public function index(Request $request): View
    {
        // Query Karya Seni (Catalog View)
        $query = Artwork::with(['user:id,name,display_name', 'category']) 
                        ->latest();
        
        // --- Implementasi Search ---
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('title', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('display_name', 'LIKE', "%{$search}%");
                  });
        }
        
        // --- Implementasi Filter Kategori ---
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $artworks = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        
        // Query Challenge Aktif (Untuk ditampilkan di homepage/catalog)
        $challenges = Challenge::query()
    // 💡 SOLUSI: Tampilkan Challenge yang sedang berjalan/akan datang
    ->where('ends_at', '>=', now()) 
    ->orWhere(function ($query) {
        // ATAU Challenge yang sudah berakhir (ends_at < now()) DAN sudah ada pemenang
        $query->where('ends_at', '<', now())
              ->whereHas('submissions', function ($q) {
                  $q->where('is_winner', true);
              });
    })
    ->orderBy('ends_at', 'ASC') // Urutkan berdasarkan waktu berakhir
    ->take(4) // Ambil sedikit lebih banyak untuk tampilan homepage
    ->get();

        // 💡 PERBAIKAN: Hapus baris 'return' yang tidak pada tempatnya.
        // return view('public.catalog.index', compact('artworks', 'categories', 'challenges')); // <--- Hapus atau Non-Aktifkan Baris Ini

        // 💡 Logika Kondisional yang Benar
        if ($request->routeIs('homepage')) {
            // Jika diakses melalui route 'homepage' (yaitu route '/')
            // Tampilkan view yang berisi preview (welcome.blade.php)
            return view('welcome', compact('artworks', 'categories', 'challenges'));
        }

        // Jika diakses melalui route lain (yaitu route 'artworks.catalog' atau /catalog)
        // Tampilkan view galeri penuh (public.catalog.index.blade.php)
        return view('public.catalog.index', compact('artworks', 'categories', 'challenges'));
    }

    /**
     * Menampilkan Halaman Detail Karya (Artwork Details Page)
     */
    public function show(Artwork $artwork): View
    {
        // Load data interaksi: likes, comments, user, etc.
        // Kita perlu memuat semua like dan favorite untuk user yang sedang login
        $artwork->load([
        'user',         
        'category', 
        'comments.user', 
        ]);
        
        // --- Logic Interaksi untuk Front-end (Mengirim Status Interaksi) ---
        $isLiked = false;
        $isFavorited = false;
        $hasReported = false;
        
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Cek apakah user sudah Like
            $isLiked = $artwork->likes()->where('user_id', $userId)->exists();
            
            // Cek apakah user sudah Favorite
            $isFavorited = $artwork->favorites()->where('user_id', $userId)->exists();
            
            // Cek apakah user sudah Report
            $hasReported = $artwork->reports()->where('user_id', $userId)->where('status', 'pending')->exists();
        }
        
        $likesCount = $artwork->likes()->count();
        
        // Kirim semua data status ke View
        return view('public.catalog.show', compact('artwork', 'likesCount', 'isLiked', 'isFavorited', 'hasReported'));
    }

    /**
     * Menampilkan Halaman Profil Kreator (Creator Profile Page)
     */
    public function showCreatorProfile(User $user): View
    {
        // Otorisasi sederhana: Menyembunyikan profil Admin jika ada
        if ($user->role === 'admin') {
            abort(404);
        }
        
        // Load karya-karya yang diunggah oleh kreator ini
        $artworks = $user->artworks()->with('category')->latest()->paginate(12);

        // Jika Anda ingin menampilkan info profil tambahan (bio, tautan eksternal),
        // Anda perlu menambahkannya di migrasi/model User.
        
        return view('public.creator.profile', compact('user', 'artworks'));
    }
}