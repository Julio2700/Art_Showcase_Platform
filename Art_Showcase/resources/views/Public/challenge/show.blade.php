@extends('layouts.app')

@section('title', 'Detail Challenge: ' . $challenge->title)

@section('content')
    
    {{-- ==========================================================
       A. SECTION HEADER & PODIUM (Hanya Tampil Jika Challenge Selesai)
       ========================================================== --}}
    
    {{-- ==========================================================
       A. SECTION HEADER & PODIUM (Hanya Tampil Jika Challenge Selesai)
       ========================================================== --}}
    
    @if ($is_over && !$winners->isEmpty())
        
        @php
            $winner1 = $winners->where('placement', 1)->first();
            $winner2 = $winners->where('placement', 2)->first();
            $winner3 = $winners->where('placement', 3)->first();
        @endphp

        <div class="mb-5 p-5 text-white shadow-lg rounded-3" 
             style="background: url('{{ asset('storage/' . $challenge->banner_path) }}') no-repeat center center; 
                    background-size: cover; 
                    min-height: 450px; 
                    position: relative;">
            
            {{-- Overlay untuk kontras teks --}}
            <div style="position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.6); border-radius: 0.5rem; z-index: 5;"></div>

            <div class="container-fluid py-4" style="z-index: 10; position: relative;"> 
                <h1 class="display-4 fw-bold text-warning mb-4">🏆 Pemenang Diumumkan!</h1>
                
                {{-- STRUKTUR PODIUM 3-1-2 --}}
                <div class="row align-items-end text-center">
                    
                    {{-- PODIUM 3 (Kiri) --}}
                    @if ($winner3)
                    <div class="col-md-4 order-md-1 mb-4">
                        <div class="p-3 bg-secondary rounded-3" style="height: 100%; border: 3px solid #8c8c8c; transform: translateY(0px);">
                            <h5 class="fw-bold">🥉 Juara 3</h5>
                            {{-- 💡 THUMBNAIL KARYA --}}
                            <img src="{{ asset('storage/' . $winner3->artwork->file_path) }}" 
                                 class="img-thumbnail mx-auto mb-2" 
                                 style="width: 100%; max-height: 100px; object-fit: cover;">
                                 
                            <a href="{{ route('artworks.show', $winner3->artwork) }}" class="text-white small">{{ Str::limit($winner3->artwork->title, 20) }}</a>
                            <p class="small mb-0 text-white-50">oleh: {{ $winner3->artwork->user->display_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- PODIUM 1 (Tengah & Paling Tinggi) --}}
                    @if ($winner1)
                    <div class="col-md-4 order-md-2 mb-0">
                        <div class="p-4 bg-warning text-dark rounded-3" style="height: 100%; border: 4px solid gold; transform: translateY(-30px);">
                            <h4 class="fw-bold">🥇 JUARA 1</h4>
                            {{-- 💡 THUMBNAIL KARYA --}}
                            <img src="{{ asset('storage/' . $winner1->artwork->file_path) }}" 
                                 class="img-thumbnail mx-auto mb-2" 
                                 style="width: 100%; max-height: 130px; object-fit: cover;">
                                 
                            <a href="{{ route('artworks.show', $winner1->artwork) }}" class="text-dark fw-bold">{{ $winner1->artwork->title }}</a>
                            <p class="small mb-0 text-dark-50">oleh: {{ $winner1->artwork->user->display_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- PODIUM 2 (Kanan) --}}
                    @if ($winner2)
                    <div class="col-md-4 order-md-3 mb-4">
                        <div class="p-3 bg-info rounded-3" style="height: 100%; border: 3px solid #0056b3; transform: translateY(-10px);">
                            <h5 class="fw-bold">🥈 Juara 2</h5>
                            {{-- 💡 THUMBNAIL KARYA --}}
                            <img src="{{ asset('storage/' . $winner2->artwork->file_path) }}" 
                                 class="img-thumbnail mx-auto mb-2" 
                                 style="width: 100%; max-height: 110px; object-fit: cover;">
                                 
                            <a href="{{ route('artworks.show', $winner2->artwork) }}" class="text-white small">{{ $winner2->artwork->title }}</a>
                            <p class="small mb-0 text-white-50">oleh: {{ $winner2->artwork->user->display_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- HEADER CHALLENGE DEFAULT (Jika belum selesai atau belum ada pemenang) --}}
        <div class="p-4 mb-4 bg-light rounded-3 shadow-sm">
            <div class="container-fluid py-2">
                <h1 class="display-5 fw-bold">{{ $challenge->title }}</h1>
                <p class="fs-5 text-muted">Diselenggarakan oleh: {{ $challenge->curator->display_name ?? $challenge->curator->name }}</p>
            </div>
        </div>
    @endif


    {{-- ==========================================================
       B. SECTION KONTEN & SUBMISSIONS
       ========================================================== --}}

    <div class="row">
        
        {{-- KOLOM KIRI: DESKRIPSI & SUBMISSIONS --}}
        <div class="col-lg-8 col-md-12">
            
            {{-- Banner Gambar (Hanya ditampilkan jika tidak ada podium) --}}
            @if (!$is_over || $winners->isEmpty())
                <div class="card mb-4 shadow-sm">
                    <img src="{{ asset('storage/' . $challenge->banner_path) }}" class="card-img-top" alt="{{ $challenge->title }} Banner">
                </div>
            @endif

            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">Aturan & Deskripsi</h4>
                    <p class="card-text">{{ $challenge->description }}</p>
                    
                    <hr class="my-4">

                    {{-- BAGIAN SUBMIT UNTUK MEMBER --}}
                    @auth
                        @if (!$is_over)
                            <div class="alert alert-success text-center">
                                <p class="mb-2">Challenge ini masih aktif!</p>
                                <a href="{{ route('member.submissions.create', $challenge) }}" class="btn btn-lg btn-success">
                                    <i class="bi bi-cloud-upload-fill me-2"></i> Submit Karya Anda
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info text-center">
                            Silakan <a href="{{ route('login') }}">Login</a> untuk berpartisipasi dalam challenge ini.
                        </div>
                    @endauth
                </div>
            </div>

            {{-- GALERI SUBMISSIONS --}}
            <h3 class="mt-5 mb-3">Galeri Submissions ({{ $submissions->total() }})</h3>
            
            @if ($submissions->isEmpty() && !$is_over)
                 <div class="alert alert-warning text-center">Belum ada submission. Jadilah yang pertama!</div>
            @else
                <div class="masonry-grid" style="column-count: 3; column-gap: 15px;"> 
                    @foreach ($submissions as $submission)
                        {{-- ... (Markup Submission Card) ... --}}
                    @endforeach
                </div>
                
                <div class="mt-4 d-flex justify-content-center">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
        
        {{-- KOLOM KANAN: STATUS --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-dark text-white">Status Challenge</div>
                <div class="card-body">
                    <p><strong>Status:</strong> 
                        @if ($is_over)
                            <span class="badge bg-danger">Telah Berakhir</span>
                        @else
                            <span class="badge bg-success">Sedang Berlangsung</span>
                        @endif
                    </p>
                    <p><strong>Periode:</strong> {{ $challenge->starts_at->format('d M Y') }} s/d {{ $challenge->ends_at->format('d M Y') }}</p>
                    @if (!$is_over)
                        <p><strong>Sisa Waktu:</strong> <span class="fw-bold text-danger">{{ $challenge->ends_at->diffForHumans() }}</span></p>
                    @endif
                </div>
            </div>
            
            {{-- Pemenang Selesai tampil di Header, jadi tidak perlu di sini --}}
            
        </div>
    </div>
@endsection