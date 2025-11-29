@extends('layouts.app')

@section('title', 'Member - Edit Karya: ' . $artwork->title)

@section('content')
    <h1 class="mb-4">Edit Karya Seni: {{ $artwork->title }}</h1>
    
    <div class="card p-4 shadow-sm">
        {{-- Form Edit, menggunakan PUT method dan enctype untuk file upload --}}
        <form action="{{ route('member.artworks.update', $artwork) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3 text-center">
                <p>Preview Gambar Saat Ini:</p>
                {{-- Tampilkan gambar saat ini --}}
                <img src="{{ asset('storage/' . $artwork->file_path) }}" alt="Current Artwork" class="img-thumbnail" style="max-height: 200px;">
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Judul Karya <span class="text-danger">*</span></label>
                {{-- Gunakan old() untuk mempertahankan input lama dan $artwork->title untuk nilai default --}}
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $artwork->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="file_upload" class="form-label">Ganti File Gambar (Opsional, maks 5MB)</label>
                <input type="file" class="form-control @error('file_upload') is-invalid @enderror" id="file_upload" name="file_upload">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                @error('file_upload') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $artwork->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi Karya (Opsional)</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $artwork->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Karya</button>
            <a href="{{ route('member.artworks.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection