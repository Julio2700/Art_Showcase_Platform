@extends('layouts.app')

@section('title', 'Member - Karya Favorit Saya')

@section('content')
    <h1 class="mb-4">Daftar Karya Favorit Saya</h1>

    @if ($favorites->isEmpty())
        <div class="alert alert-info text-center p-5">
            <h4 class="alert-heading">Daftar Favorit Kosong!</h4>
            <a href="{{ route('artworks.catalog') }}" class="btn btn-primary mt-2">Jelajahi Karya</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-4 g-4">
            @foreach ($favorites as $favorite)
                @php
                    $artwork = $favorite->artwork;
                @endphp
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/' . $artwork->file_path) }}" class="card-img-top" alt="{{ $artwork->title }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ Str::limit($artwork->title, 25) }}</h5>
                            <p class="card-text small text-muted">Kreator: {{ $artwork->user->display_name ?? $artwork->user->name }}</p>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-around">
                            <a href="{{ route('artworks.show', $artwork) }}" class="btn btn-sm btn-info text-white me-1"><i class="bi bi-eye-fill"></i> Lihat</a>
                            
                            <form action="{{ route('member.favorites.destroy', $favorite) }}" method="POST" onsubmit="return confirm('Hapus karya ini dari daftar favorit?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-x-circle-fill"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $favorites->links() }} 
        </div>
    @endif
@endsection