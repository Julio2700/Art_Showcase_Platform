@extends('layouts.app')

@section('title', 'Dashboard Curator')

@section('content')
    <h1 class="display-5 fw-bold mb-4">Selamat Datang, {{ Auth::user()->display_name ?? Auth::user()->name }}</h1>
    
    <div class="alert alert-info border-start border-5 border-info p-3">
        <i class="bi bi-megaphone-fill me-2"></i> Anda adalah **Curator**. Pusat Anda adalah **Challenge Management** dan **Pemilihan Pemenang**.
    </div>

    @php
        $curatorId = Auth::id();
        $totalChallenges = \App\Models\Challenge::where('curator_id', $curatorId)->count();
        $totalSubmissions = \App\Models\Submission::whereHas('challenge', function($query) use ($curatorId) {
            $query->where('curator_id', $curatorId);
        })->count();
        $challengesNeedReview = \App\Models\Challenge::where('curator_id', $curatorId)
                                                      ->where('ends_at', '<', now()) 
                                                      ->whereDoesntHave('submissions', function($q) {
                                                          $q->where('is_winner', true);
                                                      })
                                                      ->count();

        $challenges = \App\Models\Challenge::where('curator_id', $curatorId)
                                           ->withCount('submissions')
                                           ->latest()
                                           ->paginate(5); // Hanya 5 item untuk ringkasan
    @endphp

    {{-- KARTU RINGKASAN STATISTIK DI BAGIAN ATAS --}}
    <h2 class="mt-4 mb-3">Ringkasan & Aksi Cepat</h2>
    <div class="row mb-4">
        {{-- KARTU 1: Buat Challenge Baru --}}
        <div class="col-md-4 mb-3">
            <a href="{{ route('curator.challenges.create') }}" class="text-decoration-none d-block">
                <div class="card h-100 shadow-sm p-3 border-primary border-2">
                    <h5 class="mb-1 text-dark fw-bold"><i class="bi bi-plus-circle-fill me-2 text-primary"></i> Buat Challenge Baru</h5>
                    <p class="small text-muted mb-0">Atur aturan, tanggal mulai, dan hadiah challenge.</p>
                </div>
            </a>
        </div>
        
        {{-- KARTU 2: Total Challenges --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm p-3 border-secondary border-2">
                <h5 class="mb-1 text-dark fw-bold">Total Challenge Dibuat</h5>
                <h3 class="display-6 mb-0 text-dark">{{ $totalChallenges }}</h3>
            </div>
        </div>

        {{-- KARTU 3: Perlu Review --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm p-3 border-danger border-2">
                <h5 class="mb-1 text-danger fw-bold">Challenge Perlu Tinjauan</h5>
                <h3 class="display-6 mb-0 text-danger">{{ $challengesNeedReview }}</h3>
            </div>
        </div>
    </div>
    
    <hr class="my-4">

    {{-- DAFTAR CHALLENGE (IN-LINE) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Challenge Saya (Daftar Cepat)</h2>
        <a href="{{ route('curator.challenges.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua ({{ $totalChallenges }})</a>
    </div>

    @if ($challenges->isEmpty())
        <div class="alert alert-info text-center p-4">
            <p class="h4">Anda belum membuat challenge apa pun.</p>
            <a href="{{ route('curator.challenges.create') }}" class="btn btn-primary mt-3">Buat Challenge Baru</a>
        </div>
    @else
        <div class="row row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($challenges as $challenge)
                @php
                    $isOngoing = now()->between($challenge->starts_at, $challenge->ends_at);
                    $isOver = now()->greaterThan($challenge->ends_at);
                    $hasWinners = $challenge->submissions()->where('is_winner', true)->exists();
                    $isUpcoming = now()->lessThan($challenge->starts_at);
                @endphp
                <div class="col">
                    <div class="card h-100 shadow-sm border-{{ $isOngoing ? 'success' : ($isOver ? ($hasWinners ? 'primary' : 'danger') : 'warning') }}"
                         style="transition: all 0.3s;"
                         onmouseover="this.style.boxShadow='0 10px 20px rgba(0,0,0,0.2)'; this.style.transform='scale(1.02)'" 
                         onmouseout="this.style.boxShadow='0 2px 5px rgba(0,0,0,0.1)'; this.style.transform='scale(1)'">
                        
                        {{-- GAMBAR BANNER --}}
                        <img src="{{ asset('storage/' . $challenge->banner_path) }}" 
                             class="card-img-top" 
                             alt="{{ $challenge->title }} Banner" 
                             style="height: 150px; object-fit: cover;">
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ Str::limit($challenge->title, 30) }}</h5>
                            <p class="card-text small text-muted">Deadline: {{ $challenge->ends_at->format('d M Y') }}</p>
                            
                            {{-- STATUS BADGE --}}
                            <span class="badge bg-{{ $isOngoing ? 'success' : ($isOver ? ($hasWinners ? 'primary' : 'danger') : 'warning') }} mb-3">
                                @if ($isOngoing) Aktif
                                @elseif ($isOver && $hasWinners) Selesai (Diumumkan)
                                @elseif ($isOver && !$hasWinners) Selesai, Perlu Pemenang
                                @elseif ($isUpcoming) Akan Datang
                                @endif
                            </span>

                            {{-- TOMBOL AKSI --}}
                            <div class="mt-2">
                                @if ($isOver && !$hasWinners)
                                    <a href="{{ route('curator.submissions.index', $challenge) }}" class="btn btn-sm btn-danger w-100">Pilih Pemenang</a>
                                @else
                                    <a href="{{ route('curator.submissions.index', $challenge) }}" class="btn btn-sm btn-secondary w-100">Lihat Submissions</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-3 d-flex justify-content-center">
             <a href="{{ route('curator.challenges.index') }}" class="btn btn-outline-dark">Lihat Semua Challenge</a>
        </div>
    @endif
@endsection