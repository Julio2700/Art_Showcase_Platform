@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    
    {{-- HEADER MIRIP REFERENSI (Minimalis & Kontras) --}}
    <div class="p-4 mb-4 bg-dark text-white rounded-3 shadow-lg">
        <div class="container-fluid py-2">
            <h1 class="display-5 fw-bold">Selamat Datang, Admin</h1>
            <p class="fs-4">Ringkasan cepat dan pusat manajemen platform Art Showcase.</p>
        </div>
    </div>

    {{-- BARIS KPI/STATISTIK --}}
    <h2 class="mb-3 mt-4">Ringkasan Data</h2>
    <div class="row">
        
        {{-- KARTU 1: PENDING CURATOR --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow border-warning">
                <div class="card-body">
                    @php
                        $pendingCuratorCount = \App\Models\User::where('role', 'curator')->where('is_approved', false)->count();
                    @endphp
                    <h5 class="card-title text-warning"><i class="bi bi-person-exclamation me-2"></i> Pending Curator</h5>
                    <p class="card-text display-4 fw-bold">{{ $pendingCuratorCount }}</p>
                    <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning w-100">
                        Tinjau Sekarang
                    </a>
                </div>
            </div>
        </div>

        {{-- KARTU 2: LAPORAN PENDING --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow border-danger">
                <div class="card-body">
                    @php
                        $pendingReportCount = \App\Models\Report::where('status', 'pending')->count();
                    @endphp
                    <h5 class="card-title text-danger"><i class="bi bi-flag-fill me-2"></i> Laporan Pending</h5>
                    <p class="card-text display-4 fw-bold">{{ $pendingReportCount }}</p>
                    <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="btn btn-sm btn-danger w-100">
                        Moderasi
                    </a>
                </div>
            </div>
        </div>

        {{-- KARTU 3: TOTAL KARYA --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow border-success">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="bi bi-palette-fill me-2"></i> Total Karya</h5>
                    <p class="card-text display-4 fw-bold">{{ \App\Models\Artwork::count() }}</p>
                    <p class="text-muted small">Karya terunggah di platform.</p>
                </div>
            </div>
        </div>
    </div>
    
    <hr class="my-4">

    {{-- AKSI CEPAT / MANAJEMEN --}}
    <h2 class="mb-3">Akses Cepat Admin</h2>
    <div class="row">
        
        {{-- KARTU MANAJEMEN KATEGORI --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-tag-fill me-2"></i> Manajemen Kategori</h5>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">Kelola</a>
                </div>
            </div>
        </div>

        {{-- KARTU MANAJEMEN USER --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-people-fill me-2"></i> Manajemen User (Member, Curator, Admin)</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">Kelola</a>
                </div>
            </div>
        </div>
    </div>
@endsection