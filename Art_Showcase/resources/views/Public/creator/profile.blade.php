@extends('layouts.app')

@section('title', 'Profil Kreator: ' . $user->display_name ?? $user->name)

@section('content')

    {{-- ==========================================================
       A. PROFILE HEADER (Photo, Bio, Status)
       ========================================================== --}}
    <div class="card shadow-lg mb-5 p-4 p-md-5 text-center border-primary border-3">
        
        {{-- FOTO PROFIL --}}
        <div class="mb-3 mx-auto">
            <img src="{{ $user->avatar_path ? asset('storage/' . $user->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=007BFF&color=fff&size=100' }}" 
                 alt="Foto Profil" 
                 class="rounded-circle border border-primary border-3" 
                 style="width: 120px; height: 120px; object-fit: cover;">
        </div>
        
        {{-- NAMA & ROLE --}}
        <h1 class="display-6 fw-bold mb-1">{{ $user->display_name ?? $user->name }}</h1>
        <p class="text-muted small">@ {{ $user->name }} | Role: 
            <span class="badge bg-{{ $user->role === 'curator' ? 'info' : 'success' }}">{{ ucfirst($user->role) }}</span>
        </p>
        
        <p class="mb-3 mx-auto" style="max-width: 600px; height: 1.5rem;">
            {{ $user->bio ?? 'Silakan Edit Profil.' }}
        </p>

        {{-- ❌ TOMBOL MANAJEMEN (UNGGAH KARYA / EDIT PROFIL) DIHILANGKAN DARI SINI --}}
        
        {{-- STATISTIK PUBLIK --}}
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
    
    <hr class="my-5">

    {{-- ==========================================================
       B. PORTOFOLIO KARYA
       ========================================================== --}}
    <h2 class="mb-4 text-dark"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Portofolio Kreator</h2>

    @if ($artworks->isEmpty())
        <div class="alert alert-info text-center">Kreator ini belum mengunggah karya apa pun.</div>
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
                        <div class="card-footer bg-light text-center">
                            <a href="{{ route('artworks.show', $artwork) }}" class="btn btn-sm btn-outline-info w-100">Lihat Karya</a>
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