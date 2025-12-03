@extends('layouts.app')

@section('title', 'Galeri Karya Seni')

@section('content')
    <h1 class="mb-4">Galeri Semua Karya</h1>

    <div class="row mb-4">
        {{-- Form Pencarian dan Filter --}}
        <form action="{{ route('artworks.catalog') }}" method="GET">
            <div class="input-group">
                {{-- Filter Kategori --}}
                <select name="category_id" class="form-select" style="max-width: 250px;">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                
                {{-- Input Pencarian --}}
                <input type="text" name="search" class="form-control" placeholder="Cari judul karya atau nama kreator..." value="{{ request('search') }}">
                
                <button type="submit" class="btn btn-primary">Filter & Cari</button>
                <a href="{{ route('artworks.catalog') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card shadow h-100 border-0" 
     onmouseover="this.style.boxShadow='0 10px 20px rgba(0,0,0,0.2)'; this.style.transform='scale(1.03)'" 
     onmouseout="this.style.boxShadow='0 2px 5px rgba(0,0,0,0.1)'; this.style.transform='scale(1)'"
     style="transition: all 0.3s ease-in-out;"> 
    {{-- ... --}}
</div>

    @if ($artworks->isEmpty())
        <div class="alert alert-warning text-center">Tidak ada karya yang ditemukan berdasarkan kriteria Anda.</div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach ($artworks as $artwork)
                <div class="col">
                    <a href="{{ route('artworks.show', $artwork) }}" class="text-decoration-none text-dark">
                        <div class="card h-100 shadow-sm">
                            {{-- Image --}}
                            <img src="{{ asset('storage/' . $artwork->file_path) }}" class="card-img-top" alt="{{ $artwork->title }}" style="height: 200px; object-fit: cover;">
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ Str::limit($artwork->title, 35) }}</h5>
                                <p class="card-text small text-muted mb-1">by {{ $artwork->user->display_name ?? $artwork->user->name }}</p>
                                <span class="badge bg-secondary">{{ $artwork->category->name ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="card-footer bg-white d-flex justify-content-between">
                                <span class="small text-danger"><i class="bi bi-heart-fill"></i> {{ $artwork->likes_count ?? '0' }}</span>
                                <span class="small text-primary"><i class="bi bi-chat-dots-fill"></i> {{ $artwork->comments_count ?? '0' }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        
        <div class="mt-5 d-flex justify-content-center">
            {{ $artworks->links() }}
        </div>
    @endif
@endsection