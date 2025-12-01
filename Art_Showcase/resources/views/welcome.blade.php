@extends('layouts.app') 

@section('title', 'Platform Showcase Karya Seni Digital')

@section('content')
    <div class="jumbotron text-center bg-light p-5 rounded mb-5">
        <h1 class="display-4">Tempat Kreator Memamerkan Karyanya</h1>
        <p class="lead">Jelajahi, sukai, dan ikuti challenge dari kreator terbaik dunia.</p>
        
        {{-- TAUTAN UTAMA: MENGARAHKAN KE REGISTER JIKA INGIN IKUT INTERAKSI/KREASI --}}
        @guest
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg mt-3">Gabung Sekarang & Unggah Karya</a>
        @else
            <a href="{{ route('artworks.catalog') }}" class="btn btn-primary btn-lg mt-3">Jelajahi Galeri</a>
        @endguest
    </div>

    {{-- SECTION: CHALLENGE AKTIF --}}
    <h2 class="mb-4"><i class="bi bi-award-fill text-warning"></i> Challenge Aktif</h2>
    <div class="row">
        {{-- BLOK FORELSE DIMULAI --}}
        @forelse ($challenges as $challenge)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    
                    {{-- IMG Challenge --}}
                    <img src="{{ asset('storage/' . $challenge->banner_path) }}" 
                         class="card-img-top" 
                         alt="{{ $challenge->title }} Banner" 
                         style="height: 180px; object-fit: cover;">
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $challenge->title }}</h5>
                        <p class="card-text small text-muted">Deadline: {{ $challenge->ends_at->format('d M Y') }}</p>
                        
                        {{-- Guardrail Login --}}
                        @auth
                            <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-sm btn-outline-info me-2">Lihat Detail</a>
                            <a href="{{ route('member.submissions.create', $challenge) }}" class="btn btn-sm btn-success">Ikut Challenge</a>
                        @else
                            <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-sm btn-outline-info me-2">Lihat Detail</a>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-success">Login untuk Ikut</a>
                        @endauth
                    </div>
                </div>
            </div>
            
        @empty
            <div class="col-12">
                <div class="alert alert-info">Saat ini tidak ada challenge yang sedang berlangsung.</div>
            </div>
        @endforelse
        {{-- BLOK FORELSE SELESAI --}}
    </div>
    
    <hr class="my-5">

    {{-- SECTION: GALERI TERBARU (Filter dan Search) --}}
    <h2 class="mb-4"><i class="bi bi-palette-fill text-success"></i> Karya Terbaru</h2>
    
    <form action="{{ route('artworks.catalog') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari karya berdasarkan judul atau kreator..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>

    <div class="row">
        @forelse ($artworks->take(8) as $artwork)
            <div class="col-md-3 mb-4">
                <a href="{{ route('artworks.show', $artwork) }}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm h-100">
                        <img src="{{ asset('storage/' . $artwork->file_path) }}" class="card-img-top" alt="{{ $artwork->title }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($artwork->title, 25) }}</h6>
                            <p class="card-text small text-muted">by {{ $artwork->user->display_name ?? $artwork->user->name }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="alert alert-warning">Belum ada karya seni yang diunggah.</div>
        @endforelse
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('artworks.catalog') }}" class="btn btn-outline-dark">Lihat Semua Karya</a>
    </div>

@endsection