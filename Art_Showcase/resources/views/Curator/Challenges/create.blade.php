@extends('layouts.app')

@section('title', 'Curator - Buat Challenge Baru')

@section('content')
    <h1 class="mb-4">Buat Challenge Baru</h1>
    
    <div class="card p-4 shadow-sm">
        <form action="{{ route('curator.challenges.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Judul Challenge <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi & Aturan <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="ends_at" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                {{-- Gunakan type="datetime-local" atau sesuaikan dengan input date/time picker yang Anda gunakan --}}
                <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" required>
                @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="banner_path" class="form-label">Banner Challenge (Opsional)</label>
                <input type="file" class="form-control @error('banner_path') is-invalid @enderror" id="banner_path" name="banner_path">
                @error('banner_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Buat Challenge</button>
            <a href="{{ route('curator.challenges.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection