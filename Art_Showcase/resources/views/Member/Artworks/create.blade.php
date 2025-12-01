@extends('layouts.app')

@section('title', 'Member - Unggah Karya')

@section('content')
    <h1 class="mb-4">Unggah Karya Seni Baru</h1>
    
    <div class="card p-4 shadow-sm">
        {{-- Penting: enctype="multipart/form-data" diperlukan untuk upload file --}}
        <form action="{{ route('member.artworks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Judul Karya <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="file_upload" class="form-label">File Gambar (JPEG, PNG, GIF, maks 5MB) <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('file_upload') is-invalid @enderror" id="file_upload" name="file_upload" required>
                @error('file_upload') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                
                {{-- 💡 PERBAIKAN: Menambahkan Guardrail View --}}
                @if ($categories->isEmpty())
                    <div class="alert alert-warning p-2">
                        Belum ada kategori yang tersedia. Mohon hubungi Admin untuk menambahkannya.
                    </div>
                    <input type="hidden" name="category_id" value=""> 
                @else
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
                {{-- Akhir Perbaikan Guardrail --}}
                
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="tags" class="form-label">Tags (Pisahkan dengan koma)</label>
                <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags" name="tags" value="{{ old('tags') }}" placeholder="digital art, landscape, potret">
                @error('tags') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi Karya (Opsional)</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary" {{ $categories->isEmpty() ? 'disabled' : '' }}>Submit Karya</button>
            <a href="{{ route('member.artworks.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection