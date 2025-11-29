@extends('layouts.app')

@section('title', 'Curator - Edit Challenge: ' . $challenge->title)

@section('content')
    <h1 class="mb-4">Edit Challenge: {{ $challenge->title }}</h1>
    
    <div class="card p-4 shadow-sm">
        <form action="{{ route('curator.challenges.update', $challenge) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Judul Challenge <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $challenge->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi & Aturan <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $challenge->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="ends_at" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                {{-- Format datetime-local harus YYYY-MM-DDTHH:MM --}}
                <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at" value="{{ old('ends_at', $challenge->ends_at->format('Y-m-d\TH:i')) }}" required>
                @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                @if ($challenge->banner_path)
                    <p>Banner Saat Ini:</p>
                    <img src="{{ asset('storage/' . $challenge->banner_path) }}" alt="Current Banner" class="img-thumbnail mb-2" style="max-height: 150px;">
                @endif
                <label for="banner_path" class="form-label">Ganti Banner Challenge (Opsional)</label>
                <input type="file" class="form-control @error('banner_path') is-invalid @enderror" id="banner_path" name="banner_path">
                @error('banner_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Challenge</button>
            <a href="{{ route('curator.challenges.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection