@extends('layouts.app')

@section('title', 'Member - Karya Saya')

@section('content')
    <h1 class="mb-4">Dashboard Kreator</h1>
    <div class="d-flex justify-content-between mb-4">
        <h2>Daftar Karya Seni Saya</h2>
        <a href="{{ route('member.artworks.create') }}" class="btn btn-success">
            <i class="bi bi-cloud-upload-fill"></i> Unggah Karya Baru
        </a>
    </div>

    @if ($artworks->isEmpty())
        <div class="alert alert-info text-center p-5">
            <h4 class="alert-heading">Belum Ada Karya!</h4>
            <p>Ayo, mulai unggah karya pertama Anda dan pamerkan ke seluruh dunia!</p>
            <a href="{{ route('member.artworks.create') }}" class="btn btn-primary mt-2">Unggah Sekarang</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($artworks as $artwork)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/' . $artwork->file_path) }}" class="card-img-top" alt="{{ $artwork->title }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ Str::limit($artwork->title, 30) }}</h5>
                            <span class="badge bg-secondary">{{ $artwork->category->name ?? 'Tanpa Kategori' }}</span>
                            <p class="card-text small mt-2">Diunggah: {{ $artwork->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-around">
                            <a href="{{ route('artworks.show', $artwork) }}" class="btn btn-sm btn-info text-white me-1"><i class="bi bi-eye-fill"></i> Lihat</a>
                            <a href="{{ route('member.artworks.edit', $artwork) }}" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                            
                            <form action="{{ route('member.artworks.destroy', $artwork) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus karya ini? Semua like dan komentar akan hilang.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $artworks->links() }}
        </div>
    @endif
@endsection