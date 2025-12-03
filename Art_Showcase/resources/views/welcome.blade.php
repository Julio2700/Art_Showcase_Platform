@extends('layouts.app') 

@section('title', 'Platform Showcase Karya Seni Digital')

@section('content')

    {{-- HEADER UTAMA (JUMBOTRON) --}}
    <div class="p-5 mb-5 text-center bg-dark text-white rounded-3 shadow-lg">
        <h1 class="display-3 fw-bold text-white">𝒥𝒰𝓛𝐸𝒮</h1>
        <p class="lead fs-5 text-white">Jelajahi, sukai, dan ikuti tantangan dari kreator terbaik dunia.</p>
    </div>

    <hr class="my-5">

    {{-- 1. SECTION: GALERI TERBARU (DIATAS) --}}
    <h2 class="mb-4 display-6 fw-normal text-success"><i class="bi bi-palette-fill me-2"></i> Karya Terbaru</h2>
    
    {{-- Form Pencarian/Filter --}}
    <form action="{{ route('artworks.catalog') }}" method="GET" class="mb-4">
        <div class="input-group shadow-sm">
            <input type="text" name="search" class="form-control form-control-lg" placeholder="Cari karya berdasarkan judul atau kreator..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary btn-lg">Cari</button>
        </div>
    </form>

    {{-- 💡 MASONRY GRID DENGAN PINTEREST HOVER EFFECT --}}
    <div class="masonry-grid">
        @forelse ($artworks->take(8) as $artwork)
            <div class="masonry-item"> 
                <a href="{{ route('artworks.show', $artwork) }}" class="card-link-wrapper text-decoration-none text-dark d-block">
                    
                    {{-- Tambahkan class 'pinterest-card' untuk styling --}}
                    <div class="card shadow border-0 pinterest-card" 
                         style="transition: all 0.3s;"
                         onmouseover="this.style.boxShadow='0 15px 30px rgba(0,0,0,0.3)'; this.style.transform='translateY(-5px)'" 
                         onmouseout="this.style.boxShadow='0 2px 5px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)'"
                    >
                        
                        <div class="img-wrapper">
                            {{-- Gambar dengan rasio asli --}}
                            <img src="{{ asset('storage/' . $artwork->file_path) }}" 
                                 class="card-img-top" 
                                 alt="{{ $artwork->title }}" 
                                 style="width: 100%; height: auto; display: block; object-fit: cover;">
                            
                            {{-- 💡 ELEMEN OVERLAY PINTEREST --}}
                            <div class="card-overlay">
                                <div class="overlay-content">
                                    <i class="bi bi-eye-fill h4 mb-2 text-white"></i>
                                    <p class="mb-0 text-white small">Lihat Detail</p>
                                </div>
                            </div>
                        </div> {{-- End img-wrapper --}}
                        
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-1">{{ Str::limit($artwork->title, 25) }}</h6>
                            <p class="card-text small text-muted mb-0">by {{ $artwork->user->display_name ?? $artwork->user->name }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="alert alert-warning col-12">Belum ada karya seni yang diunggah.</div>
        @endforelse
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('artworks.catalog') }}" class="btn btn-outline-dark btn-lg">Lihat Semua Karya</a>
    </div>

    <hr class="my-5">

    {{-- 2. SECTION: CHALLENGE AKTIF (DI BAWAH) --}}
    <h2 class="mb-4 display-6 fw-normal text-warning"><i class="bi bi-award-fill me-2"></i> Challenge Aktif</h2>
    <div class="row">
        @forelse ($challenges as $challenge)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-warning">
                    
                    <img src="{{ asset('storage/' . $challenge->banner_path) }}" 
                         class="card-img-top" 
                         alt="{{ $challenge->title }} Banner" 
                         style="height: 180px; object-fit: cover;">
                    
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $challenge->title }}</h5>
                        <p class="card-text small text-danger">Deadline: {{ $challenge->ends_at->format('d M Y') }}</p>
                        
                        @auth
                            @if (Auth::user()->role === 'member')
                                <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-sm btn-outline-info me-2">Lihat Detail</a>
                                <a href="{{ route('member.submissions.create', $challenge) }}" class="btn btn-sm btn-success">Submit Karya</a>
                            @else
                                <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-sm btn-info w-100">Lihat Detail</a>
                            @endif
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
    </div>
    
@endsection