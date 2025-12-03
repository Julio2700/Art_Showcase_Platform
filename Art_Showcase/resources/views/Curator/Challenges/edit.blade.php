@extends('layouts.app')

@section('title', 'Curator - Edit Challenge: ' . $challenge->title)

@section('content')
    <h1 class="mb-4">Edit Challenge: {{ $challenge->title }}</h1>
    
    <div class="card p-4 shadow-sm">
        {{-- Form Edit, menggunakan PUT method dan enctype untuk file upload --}}
        <form action="{{ route('curator.challenges.update', $challenge) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3 text-center">
                <p>Preview Banner Saat Ini:</p>
                <img src="{{ asset('storage/' . $challenge->banner_path) }}" alt="Current Banner" class="img-thumbnail" style="max-height: 150px;">
                <p class="text-muted small mt-1">Kosongkan file di bawah jika tidak ingin mengganti banner.</p>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Judul Challenge <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $challenge->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="banner_path" class="form-label">Ganti Banner Challenge (Opsional, maks 3MB)</label>
                <input type="file" class="form-control @error('banner_path') is-invalid @enderror" id="banner_path" name="banner_path">
                @error('banner_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="starts_at" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    {{-- Format tanggal/waktu harus disiapkan agar sesuai dengan input datetime-local --}}
                    <input type="datetime-local" class="form-control" name="starts_at" 
                    value="{{ old('starts_at', $challenge->starts_at?->format('Y-m-d\TH:i')) }}" required>
                    @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ends_at" class="form-label">Tanggal Berakhir (Deadline) <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at" value="{{ old('ends_at', $challenge->ends_at?->format('Y-m-d\TH:i')) }}" required>
                    @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi & Aturan Challenge <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="7" required>{{ old('description', $challenge->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-3">Perbarui Challenge</button>
            <a href="{{ route('curator.challenges.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
@endsection