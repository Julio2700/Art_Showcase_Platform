@extends('layouts.app')

@section('title', 'Dashboard Curator')

@section('content')
    <h1 class="mb-4">Selamat Datang, {{ Auth::user()->display_name ?? Auth::user()->name }}</h1>
    
    <div class="alert alert-info">
        Anda adalah seorang **Curator**. Kelola challenge Anda dan tinjau submissions.
    </div>

    {{-- Ambil data statistik dari Controller (jika Anda menambahkannya di HomeController) --}}
    @php
        // Menggunakan query langsung di View untuk data cepat (praktik umum di dashboard)
        $curatorId = Auth::id();
        $totalChallenges = \App\Models\Challenge::where('curator_id', $curatorId)->count();
        $totalSubmissions = \App\Models\Submission::whereHas('challenge', function($query) use ($curatorId) {
            $query->where('curator_id', $curatorId);
        })->count();
        $challengesNeedReview = \App\Models\Challenge::where('curator_id', $curatorId)
                                                      ->where('ends_at', '<', now()) // Challenge yang sudah berakhir
                                                      ->whereDoesntHave('submissions', function($q) {
                                                          $q->where('is_winner', true); // Tapi belum ada pemenang
                                                      })
                                                      ->count();
    @endphp

    <div class="row">
        {{-- KARTU 1: Total Challenge Dibuat --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Challenge Anda</h5>
                    <p class="card-text display-4">{{ $totalChallenges }}</p>
                    <a href="{{ route('curator.challenges.index') }}" class="btn btn-sm btn-light text-primary">Kelola Challenge <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- KARTU 2: Total Submissions Diterima --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Submissions Masuk</h5>
                    <p class="card-text display-4">{{ $totalSubmissions }}</p>
                    <p class="mb-0 small">Karya yang dikirim ke semua challenge Anda.</p>
                </div>
            </div>
        </div>

        {{-- KARTU 3: Challenge Perlu Review (Memilih Pemenang) --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Perlu Dipilih Pemenang</h5>
                    <p class="card-text display-4">{{ $challengesNeedReview }}</p>
                    <a href="{{ route('curator.challenges.index') }}" class="btn btn-sm btn-light text-danger">Tinjau & Pilih <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <h3 class="mt-4">Aksi Cepat</h3>
    <div class="list-group shadow-sm">
        <a href="{{ route('curator.challenges.create') }}" class="list-group-item list-group-item-action list-group-item-success">
            <i class="bi bi-plus-circle-fill me-2"></i> **Buat Challenge Baru**
        </a>
        <a href="{{ route('curator.challenges.index') }}" class="list-group-item list-group-item-action">
            <i class="bi bi-list-task me-2"></i> Kelola Semua Challenge Anda
        </a>
    </div>
@endsection