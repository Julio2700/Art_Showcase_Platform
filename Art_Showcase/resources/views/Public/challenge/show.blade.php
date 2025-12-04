@extends('layouts.app')

@section('title', 'Detail Challenge: ' . $challenge->title)

@section('content')
    
    {{-- HEADER CHALLENGE --}}
    <div class="p-4 mb-4 bg-light rounded-3 shadow-sm">
        <div class="container-fluid py-2">
            <h1 class="display-5 fw-bold">{{ $challenge->title }}</h1>
            <p class="fs-5 text-muted">Diselenggarakan oleh: {{ $challenge->curator->display_name ?? $challenge->curator->name }}</p>
        </div>
    </div>
    
    <div class="row">
        
        {{-- KOLOM KIRI: DESKRIPSI & SUBMISSIONS --}}
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4 shadow-sm">
                <img src="{{ asset('storage/' . $challenge->banner_path) }}" class="card-img-top" alt="{{ $challenge->title }} Banner">
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
                {{-- 💡 PERBAIKAN: Menggunakan MASONRY GRID untuk tampilan responsif --}}
                <div class="masonry-grid" style="column-count: 3; column-gap: 15px;"> 
                    @foreach ($submissions as $submission)
                        <div class="masonry-item">
                            <a href="{{ route('artworks.show', $submission->artwork) }}" class="text-decoration-none text-dark d-block">
                                <div class="card shadow-sm mb-4 pinterest-card" style="transition: all 0.3s;">
                                    {{-- Image --}}
                                    <div class="img-wrapper">
                                        <img src="{{ asset('storage/' . $submission->artwork->file_path) }}" 
                                             class="card-img-top" 
                                             alt="{{ $submission->artwork->title }}" 
                                             style="width: 100%; height: auto; display: block; object-fit: cover;">
                                        <div class="card-overlay">
                                            <div class="overlay-content">
                                                <i class="bi bi-eye-fill h4 mb-2 text-white"></i>
                                                <p class="mb-0 text-white small">Lihat Karya</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-2">
                                        <p class="card-title small mb-1 fw-bold">{{ Str::limit($submission->artwork->title, 20) }}</p>
                                        <p class="small text-muted mb-0">by {{ $submission->artwork->user->display_name ?? $submission->artwork->user->name }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 d-flex justify-content-center">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
        
        {{-- KOLOM KANAN: STATUS & PEMENANG --}}
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
            
            {{-- HASIL PEMENANG --}}
            @if ($is_over)
                <ul class="list-group list-group-flush">
                       @forelse ($winners as $winner)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Juara {{ $winner->placement }}</strong>
                            <span>
                                <a href="{{ route('artworks.show', $winner->artwork) }}" class="text-decoration-none text-primary">
                                    {{ Str::limit($winner->artwork->title, 20) }}
                                </a>
                                {{-- 💡 PASTIKAN MENGGUNAKAN NULLSAFE OPERATOR --}}
                                (oleh {{ $winner->artwork->user?->display_name ?? $winner->artwork->user?->name ?? 'Kreator Dihapus' }})
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">Pemenang belum ditetapkan.</li>
                    @endforelse
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection