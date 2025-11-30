@extends('layouts.app')

@section('title', 'Dashboard Kreator')

@section('content')
    <h1 class="mb-4">Selamat Datang, {{ Auth::user()->display_name ?? Auth::user()->name }}</h1>
    
    <div class="alert alert-info">
        Ini adalah Dashboard Kreator Anda. Anda dapat mengelola karya, melihat favorit, dan berpartisipasi dalam challenge.
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Karya Seni Diunggah</h5>
                    <p class="card-text display-4">{{ \App\Models\Artwork::where('user_id', Auth::id())->count() }}</p>
                    {{-- TOMBOL KELOLA KARYA (member.artworks.index) --}}
                    <a href="{{ route('member.artworks.index') }}" class="btn btn-sm btn-light text-primary">Kelola Karya <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Karya di Favoritkan</h5>
                    <p class="card-text display-4">{{ \App\Models\Favorite::where('user_id', Auth::id())->count() }}</p>
                    {{-- TOMBOL LIHAT FAVORIT (member.favorites.index) --}}
                    <a href="{{ route('member.favorites.index') }}" class="btn btn-sm btn-dark">Lihat Favorit <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Aksi Cepat</h5>
                    <a href="{{ route('member.artworks.create') }}" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-cloud-upload-fill"></i> Unggah Karya Baru
                    </a>
                    <a href="{{ route('member.artworks.index') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Galeri Pribadi
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection