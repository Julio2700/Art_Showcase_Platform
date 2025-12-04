@extends('layouts.app')

@section('title', 'Profile & Portofolio Saya')

@section('content')

    {{-- Ambil data dari Auth dan Model --}}
    @php
        $user = Auth::user();
        $artworks = $memberArtworks; // Artworks yang dikirim dari Controller
        $totalArtworks = $user->artworks()->count();
        $totalFavorites = $user->favorites()->count(); 
        $totalLikesReceived = \App\Models\Like::whereHas('artwork', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    @endphp

    <div class="card shadow-lg mb-5 p-4 p-md-5 text-center border-primary border-3">
        
        {{-- 💡 FOTO PROFIL (Avatar di tengah) --}}
        <div class="mb-3 mx-auto"> {{-- mx-auto untuk menengahkan div --}}
            <img src="{{ $user->avatar_path ? asset('storage/' . $user->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=007BFF&color=fff&size=100' }}" 
                 alt="Foto Profil" 
                 class="rounded-circle border border-primary border-3" 
                 style="width: 120px; height: 120px; object-fit: cover;">
        </div>
        
        {{-- NAMA & ROLE --}}
        {{-- NAMA & ROLE --}}
        <h1 class="display-6 fw-bold mb-1">{{ $user->display_name ?? $user->name }}</h1>
        <p class="text-muted small">@ {{ $user->name }} | Role: 
            <span class="badge bg-success">{{ ucfirst($user->role) }}</span>
        </p>
        
        {{-- 💡 PERBAIKAN: Logic Display Bio --}}
        <p class="mb-3 mx-auto" style="max-width: 600px;">
            @if ($user->bio)
                 {{ $user->bio }}
            @else
                 WELCOME TO MY PROFILE
            @endif
        </p>

        {{-- QUICK ACTIONS & STATS --}}
        <div class="row justify-content-center mt-4">
            <div class="col-4 col-md-2">
                <h4 class="fw-bold mb-0">{{ $totalArtworks }}</h4>
                <p class="small text-muted">Karya</p>
            </div>
            <div class="col-4 col-md-2">
                <h4 class="fw-bold mb-0">{{ $totalLikesReceived }}</h4>
                <p class="small text-muted">Likes Diterima</p>
            </div>
            <div class="col-4 col-md-2">
                <h4 class="fw-bold mb-0">{{ $totalFavorites }}</h4>
                <p class="small text-muted">Favorit</p>
            </div>
        </div>
    </div>

    <h2 class="mt-4 mb-3">Aksi Cepat</h2>
    <div class="row">
        
        {{-- KARTU AKSI 1: Unggah Karya --}}
        <div class="col-md-6 mb-4">
            <a href="{{ route('member.artworks.create') }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm p-4 border-success border-2 hover-shadow-lg">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-cloud-upload-fill h2 text-success me-3"></i>
                        <div>
                            <h5 class="mb-1 text-dark fw-bold">Unggah Karya Baru</h5>
                            <p class="small text-muted mb-0">Tambahkan karya terbaru ke portofolio Anda.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        {{-- KARTU AKSI 2: Edit Profil --}}
        <div class="col-md-6 mb-4">
            <a href="{{ route('profile.edit') }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm p-4 border-primary border-2 hover-shadow-lg">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-fill-gear h2 text-primary me-3"></i>
                        <div>
                            <h5 class="mb-1 text-dark fw-bold">Edit Profil & Akun</h5>
                            <p class="small text-muted mb-0">Kelola info publik, foto, dan password Anda.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <hr class="my-5">

    <h2 class="mb-4 text-dark"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Portofolio Saya</h2>

    @if ($artworks->isEmpty())
        <div class="alert alert-info text-center">Anda belum mengunggah karya apa pun.</div>
    @else
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach ($artworks as $artwork)
                <div class="col">
                    <div class="card shadow-sm h-100 border-0 pinterest-card" style="transition: all 0.3s;">
                        <div class="img-wrapper">
                            <img src="{{ asset('storage/' . $artwork->file_path) }}" 
                                 class="card-img-top" 
                                 alt="{{ $artwork->title }}" 
                                 style="width: 100%; height: auto; display: block; object-fit: cover;">
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-1">{{ Str::limit($artwork->title, 25) }}</h6>
                            <p class="card-text small text-muted mb-0">Likes: {{ $artwork->likes_count ?? 0 }} | Fav: {{ $artwork->favorites_count ?? 0 }}</p>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-between">
                            <a href="{{ route('artworks.show', $artwork) }}" class="btn btn-sm btn-outline-info">Lihat</a>
                            <a href="{{ route('member.artworks.edit', $artwork) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                            
                            {{-- Delete Form --}}
                            <form action="{{ route('member.artworks.destroy', $artwork) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen karya ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {{ $artworks->links() }}
        </div>
    @endif
@endsection