@extends('layouts.app')

@section('title', 'Curator - Pilih Pemenang: ' . $challenge->title)

@section('content')
    <h1 class="mb-4">Pilih Pemenang Challenge: {{ $challenge->title }}</h1>
    
    <div class="alert alert-warning">
        Challenge ini telah berakhir pada **{{ $challenge->ends_at->format('d F Y H:i') }}**. Silakan pilih 3 pemenang (Juara 1, 2, 3) di bawah ini.
    </div>

    @if ($submissions->isEmpty())
        <div class="alert alert-info text-center">Tidak ada submission yang tersedia untuk challenge ini.</div>
    @else
        {{-- PASTIKAN FORM TERTUTUP DI BAGIAN AKHIR FORM --}}
        <form action="{{ route('curator.challenges.store_winners', $challenge) }}" method="POST">
            @csrf
            
            <div class="card p-4 mb-4 shadow-sm">
                <h4 class="mb-3">Pilihan Pemenang</h4>
                
                {{-- Juara 1 --}}
                <div class="mb-3">
                    <label for="winner_1" class="form-label h5 text-success">Juara 1 <span class="text-danger">*</span></label>
                    <select class="form-select @error('winner_1') is-invalid @enderror" id="winner_1" name="winner_1" required>
                        <option value="">Pilih Karya Pemenang Pertama</option>
                        @foreach ($submissions as $submission)
                            <option value="{{ $submission->id }}" {{ old('winner_1') == $submission->id ? 'selected' : '' }}>
                                #{{ $submission->id }} - {{ $submission->artwork->title }} (by {{ $submission->artwork->user->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('winner_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Juara 2 --}}
                <div class="mb-3">
                    <label for="winner_2" class="form-label h5 text-primary">Juara 2 <span class="text-danger">*</span></label>
                    <select class="form-select @error('winner_2') is-invalid @enderror" id="winner_2" name="winner_2" required>
                        <option value="">Pilih Karya Pemenang Kedua</option>
                        @foreach ($submissions as $submission)
                            <option value="{{ $submission->id }}" {{ old('winner_2') == $submission->id ? 'selected' : '' }}>
                                #{{ $submission->id }} - {{ $submission->artwork->title }} (by {{ $submission->artwork->user->name }})
                            </option>
                        @endforeach
                    </select>
                    {{-- ✅ PERBAIKAN SINTAKS: Mengganti @endror menjadi @enderror --}}
                    @error('winner_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Juara 3 --}}
               <div class="mb-3">
    <label for="winner_3" class="form-label h5 text-info">Juara 3</label> {{-- ❌ Hapus <span class="text-danger">*</span> --}}
    <select class="form-select @error('winner_3') is-invalid @enderror" id="winner_3" name="winner_3"> {{-- ❌ Hapus required --}}
        <option value="">Pilih Karya Pemenang Ketiga (Opsional)</option>
        @foreach ($submissions as $submission)
            @endforeach
    </select>
    @error('winner_3') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
                
                <button type="submit" class="btn btn-success btn-lg mt-4" onclick="return confirm('Pastikan Anda telah memilih 3 karya yang berbeda. Proses ini akan menetapkan pemenang challenge.')">
                    Tetapkan Pemenang
                </button>
            </div>
        </form>
        
        <h4 class="mb-3 mt-5">Daftar Submissions untuk Review</h4>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            @foreach ($submissions as $submission)
                <div class="col">
                    <a href="{{ route('artworks.show', $submission->artwork) }}" target="_blank" class="text-decoration-none text-dark">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ asset('storage/' . $submission->artwork->file_path) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Artwork">
                            <div class="card-body">
                                <h6 class="card-title">{{ Str::limit($submission->artwork->title, 25) }}</h6>
                                <p class="card-text small text-muted">by {{ $submission->artwork->user->name }}</p>
                            </div>
                            <div class="card-footer text-center">
                                <small class="text-muted">Submitted: {{ $submission->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">
    @endif
@endsection