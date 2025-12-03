@extends('layouts.app')

@section('title', 'Submit Karya | ' . $challenge->title)

@section('content')
    <h1 class="mb-4">Submit Karya ke Challenge</h1>
    <p class="lead">Challenge Aktif: <strong>{{ $challenge->title }}</strong></p>
    <p class="text-muted">Deadline: {{ $challenge->ends_at->format('d F Y H:i') }}</p>

    <div class="card p-4 shadow-sm">
        <h3 class="mb-3">Pilih Karya Anda</h3>
        
        {{-- Form Submission --}}
        <form action="{{ route('member.submissions.store', $challenge) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="artwork_id" class="form-label">Karya yang Akan Di-Submit <span class="text-danger">*</span></label>
                
                @if ($artworks->isEmpty())
                    <div class="alert alert-warning">
                        Anda belum mengunggah karya apa pun. Silakan <a href="{{ route('member.artworks.create') }}" class="alert-link">Unggah Karya Baru</a> terlebih dahulu.
                    </div>
                    {{-- Input disembunyikan dan Submit dinonaktifkan jika tidak ada karya --}}
                    <input type="hidden" name="artwork_id" value="">
                @else
                    <select class="form-select @error('artwork_id') is-invalid @enderror" id="artwork_id" name="artwork_id" required>
                        <option value="">Pilih Karya dari Galeri Anda</option>
                        @foreach ($artworks as $artwork)
                            <option value="{{ $artwork->id }}" {{ old('artwork_id') == $artwork->id ? 'selected' : '' }}>
                                {{ $artwork->title }} ({{ $artwork->created_at->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                @endif
                
                @error('artwork_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <p class="small text-muted">Hanya satu karya yang dapat di-submit per challenge. Memilih karya di sini tidak akan menghapus atau mengubah karya asli Anda.</p>
            </div>

            <button type="submit" class="btn btn-primary" {{ $artworks->isEmpty() ? 'disabled' : '' }}>Submit ke Challenge</button>
            <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection