@extends('layouts.app')

@section('title', 'Dashboard Curator')

@section('content')
    <h1 class="mb-4 display-5 fw-bold">Selamat Datang, {{ Auth::user()->display_name ?? Auth::user()->name }}</h1>
    
    <div class="alert alert-info border-start border-5 border-info">
        <i class="bi bi-megaphone-fill me-2"></i> Anda adalah seorang **Curator**. Pusat Anda adalah **Challenge Management** dan **Pemilihan Pemenang**.
        
        {{-- 💡 PERBAIKAN NAVIGASI: Tambahkan link ke Admin Dashboard jika pengguna juga Admin --}}
        @if (Auth::user()->role === 'admin')
            <hr class="my-2">
            <a href="{{ route('admin.dashboard') }}" class="alert-link small fw-bold">
                <i class="bi bi-arrow-left-circle-fill"></i> Kembali ke Admin Dashboard
            </a>
        @endif
    </div>

    <h2 class="mt-5 mb-3">Ringkasan Challenge</h2>
    
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
    @endphp

    <div class="row">
        
        {{-- KARTU 1: TOTAL CHALLENGE DIBUAT --}}
        <div class="col-md-4 mb-4">
    <div class="card h-100 shadow bg-primary text-white">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-award-fill me-2"></i> Challenge Dibuat</h5>
            <p class="card-text display-4 fw-bold text-white">{{ $totalChallenges }}</p>
            <a href="{{ route('curator.challenges.index') }}" class="btn btn-sm btn-light text-primary w-100">Kelola Semua Challenge</a> 
        </div>
    </div>
</div>

        {{-- KARTU 2: TOTAL SUBMISSIONS DITERIMA --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-inbox-fill me-2"></i> Total Submissions</h5>
                    <p class="card-text display-4 fw-bold">{{ $totalSubmissions }}</p>
                    <p class="mb-0 small">Karya yang dikirim ke semua challenge Anda.</p>
                </div>
            </div>
        </div>

        {{-- KARTU 3: CHALLENGE PERLU REVIEW (KRITIS) --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-search me-2"></i> Perlu Dipilih Pemenang</h5>
                    <p class="card-text display-4 fw-bold">{{ $challengesNeedReview }}</p>
                    <a href="{{ route('curator.challenges.index') }}" class="btn btn-sm btn-light text-danger w-100">Tinjau & Pilih Pemenang</a>
                </div>
            </div>
        </div>
    </div>
    
    <hr class="my-5">

    <h2 class="mb-3">Aksi Cepat</h2>
    <div class="row">
        <div class="col-md-6">
            <a href="{{ route('curator.challenges.create') }}" class="card-link text-decoration-none">
                <div class="card shadow-sm p-3 border-success border-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-plus-circle-fill h2 text-success me-3"></i>
                        <div>
                            <h5 class="mb-1 text-dark">Buat Challenge Baru</h5>
                            <p class="small text-muted mb-0">Atur aturan, tanggal mulai, dan hadiah challenge.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('curator.challenges.index') }}" class="card-link text-decoration-none">
                <div class="card shadow-sm p-3 border-primary border-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-list-task h2 text-primary me-3"></i>
                        <div>
                            <h5 class="mb-1 text-dark">Kelola Challenge Anda</h5>
                            <p class="small text-muted mb-0">Edit, hapus, atau tinjau submissions challenge yang ada.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection