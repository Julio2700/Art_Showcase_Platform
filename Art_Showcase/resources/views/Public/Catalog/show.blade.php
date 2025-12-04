@extends('layouts.app')

@section('title', 'Detail Karya: ' . $artwork->title)

@section('content')
    <div class="row">
        
        {{-- KOLOM KIRI: KARYA SENI & KOMENTAR --}}
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm">
                <div class="text-center bg-light p-3">
                    <img src="{{ asset('storage/' . $artwork->file_path) }}" class="img-fluid rounded" alt="{{ $artwork->title }}" style="max-height: 80vh; object-fit: contain;">
                </div>
                <div class="card-body">
                    <h1 class="card-title display-6">{{ $artwork->title }}</h1>
                    <p class="text-muted">Kategori: <span class="badge bg-secondary">{{ $artwork->category->name ?? 'Uncategorized' }}</span></p>
                    <hr>
                    <p class="card-text">{{ $artwork->description }}</p>
                    
                    @if (!empty($artwork->tags))
                        <div class="mt-3">
                            @foreach (is_array($artwork->tags) ? $artwork->tags : explode(',', $artwork->tags) as $tag)
                                <span class="badge bg-info text-dark me-1">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <img src="{{ $artwork->user->avatar_path ? asset('storage/' . $artwork->user->avatar_path) : 'https://via.placeholder.com/100/CCCCCC/FFFFFF?text=AVATAR' }}" 
         class="rounded-circle mb-3" alt="Avatar" style="width: 100px; height: 100px; object-fit: cover;">
            
            {{-- Bagian Komentar --}}
            <h3 class="mb-3"><i class="bi bi-chat-dots-fill"></i> Komentar ({{ $artwork->comments->count() }})</h3>
            
            @auth
                {{-- Form Tambah Komentar (Hanya untuk User yang Login) --}}
                <div class="card mb-4 p-3 bg-light">
                    <form action="{{ route('member.interact.comment', $artwork) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="2" placeholder="Bergabung dalam percakapan..." required>{{ old('content') }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Kirim Komentar</button>
                    </form>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <a href="{{ route('login') }}" class="alert-link fw-bold">Login</a> untuk dapat memberikan komentar.
                </div>
            @endauth

            {{-- Daftar Komentar --}}
            <div class="list-group">
                @forelse ($artwork->comments as $comment)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 text-primary">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $comment->content }}</p>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted">Belum ada komentar. Jadilah yang pertama berkomentar!</div>
                @endforelse
            </div>
        </div>

        {{-- KOLOM KANAN: KREATOR & INTERAKSI --}}
        <div class="col-lg-4">
            
            {{-- Info Kreator --}}
            <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white text-center">Kreator</div>
            <div class="card-body text-center">
                {{-- ✅ PASTIKAN LINK INI ADA --}}
                <h5><a href="{{ route('creator.profile', $artwork->user) }}" class="text-decoration-none fw-bold">
                    {{ $artwork->user->display_name ?? $artwork->user->name }}
                </a></h5>
                <p class="badge bg-secondary">{{ $artwork->user->role }}</p>
                </div>
            </div>

            {{-- Interaksi --}}
            @auth
                @php
                    // Ambil status interaksi dari model yang sudah di-load di Controller
                    $isLiked = $artwork->likes->contains('user_id', Auth::id());
                    $isFavorited = $artwork->favorites->contains('user_id', Auth::id());
                    $hasReported = $artwork->reports->contains('user_id', Auth::id());
                @endphp

                <div class="card mb-4 p-3 shadow-sm">
                    <h5 class="mb-3">Aksi Interaksi</h5>
                    <div class="d-grid gap-2">
                        
                        {{-- 1. Like/Unlike --}}
                        <form action="{{ route('member.interact.like', $artwork) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $isLiked ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="bi bi-heart-fill"></i> {{ $isLiked ? 'Batal Suka' : 'Suka' }} ({{ $likesCount }})
                            </button>
                        </form>
                        
                        {{-- 2. Favorite/Unfavorite --}}
                        <form action="{{ route('member.interact.favorite', $artwork) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $isFavorited ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="bi bi-star-fill"></i> {{ $isFavorited ? 'Batal Favorit' : 'Favoritkan' }}
                            </button>
                        </form>

                        {{-- 3. Report Content (Trigger Modal) --}}
                        <button type="button" class="btn btn-outline-secondary mt-2" data-bs-toggle="modal" data-bs-target="#reportModal" {{ $hasReported ? 'disabled' : '' }}>
                            <i class="bi bi-flag-fill"></i> Laporkan Karya
                        </button>
                        @if ($hasReported)
                            <small class="text-danger mt-1 text-center">Karya ini sedang dalam antrean moderasi.</small>
                        @endif

                    </div>
                </div>
            @else
                 <div class="alert alert-info text-center shadow-sm">
                    Silakan <a href="{{ route('login') }}" class="alert-link fw-bold">Login</a> untuk fitur interaksi.
                </div>
            @endauth
        </div>
    </div>
    
    {{-- Modal untuk Laporan --}}
    @auth
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="reportModalLabel">Laporkan Karya</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('member.interact.report', $artwork) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Anda akan melaporkan karya **{{ $artwork->title }}**. Jelaskan alasan Anda:</p>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Pelaporan</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required placeholder="Contoh: Mengandung konten tidak pantas, melanggar hak cipta, atau spam."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endauth

@endsection