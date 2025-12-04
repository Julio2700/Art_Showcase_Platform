<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; 

class ArtworkCatalogController extends Controller
{
    public function index(Request $request): View
    {
        // Query Karya Seni (Catalog View)
        $query = Artwork::with(['user:id,name,display_name', 'category']) 
                        ->withCount(['likes', 'favorites', 'comments']) 
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
        

        $challenges = Challenge::query()

        ->where('ends_at', '>=', now()) 
        ->orWhere(function ($query) {

            $query->where('ends_at', '<', now())
                ->whereHas('submissions', function ($q) {
                    $q->where('is_winner', true);
                });
        })
        ->orderByRaw("CASE WHEN ends_at >= NOW() THEN 0 ELSE 1 END") 
        ->orderBy('ends_at', 'ASC') 
        ->take(4) 
        ->get();

       
        if ($request->routeIs('homepage')) {

            return view('welcome', compact('artworks', 'categories', 'challenges'));
        }


        return view('public.catalog.index', compact('artworks', 'categories', 'challenges'));
    }

    public function show(Artwork $artwork): View
    {

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


    public function showCreatorProfile(User $user): View
{
    // Otorisasi sederhana: Menyembunyikan profil Admin jika ada
    if ($user->role === 'admin') {
        abort(404);
    }
    
    // Load karya-karya yang diunggah oleh kreator ini
    $artworks = $user->artworks()->withCount(['likes', 'favorites'])->with('category')->latest()->paginate(12);


    $totalArtworks = $user->artworks()->count();
    $totalFavorites = $user->favorites()->count();
    $totalLikesReceived = \App\Models\Like::whereHas('artwork', function($query) use ($user) {
        $query->where('user_id', $user->id);
    })->count();
    
    return view('public.creator.profile', compact('user', 'artworks', 'totalArtworks', 'totalFavorites', 'totalLikesReceived'));
    
    }
}