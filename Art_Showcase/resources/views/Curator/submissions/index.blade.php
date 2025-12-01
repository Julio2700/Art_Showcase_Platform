@extends('layouts.app')

@section('title', 'Curator - Tinjau Submissions: ' . $challenge->title)

@section('content')
    <h1 class="mb-4">Tinjau Submissions</h1>
    <p class="lead">Challenge: <strong>{{ $challenge->title }}</strong></p>
    <p class="text-muted">Deadline: {{ $challenge->ends_at->format('d F Y H:i') }} ({{ $challenge->ends_at->diffForHumans() }})</p>

    {{-- Tombol Aksi Kritis: Pilih Pemenang --}}
    @php
        $isOver = now()->greaterThan($challenge->ends_at);
        $hasWinners = $challenge->submissions()->where('is_winner', true)->exists();
    @endphp

    <div class="d-flex justify-content-between mb-4">
        @if ($isOver && !$hasWinners)
            <div class="alert alert-danger p-2 mb-0">Challenge Selesai! Pilih pemenang sekarang.</div>
            {{-- Mengarahkan ke view pemilihan pemenang (winners.blade.php) --}}
            <a href="{{ route('curator.challenges.show_winners', $challenge) }}" class="btn btn-lg btn-danger">
                <i class="bi bi-trophy-fill me-2"></i> Pilih Pemenang
            </a>
        @elseif ($hasWinners)
             <div class="alert alert-success p-2 mb-0">Pemenang telah ditetapkan.</div>
             <a href="{{ route('curator.challenges.show_winners', $challenge) }}" class="btn btn-lg btn-success">
                <i class="bi bi-trophy-fill me-2"></i> Lihat Pemenang
            </a>
        @else
            <div class="alert alert-warning p-2 mb-0">Challenge masih aktif (berakhir {{ $challenge->ends_at->diffForHumans() }}).</div>
        @endif
    </div>

    <hr>
    <h3 class="mt-4 mb-3">Galeri Submissions ({{ $submissions->count() }})</h3>

    @if ($submissions->isEmpty())
        <div class="alert alert-info text-center">Belum ada karya yang di-submit untuk challenge ini.</div>
    @else
        <div class="row row-cols-1 row-cols-md-4 g-4">
            @foreach ($submissions as $submission)
                @php
                    $artwork = $submission->artwork;
                @endphp
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        {{-- Image --}}
                        <img src="{{ asset('storage/' . $artwork->file_path) }}" class="card-img-top" alt="{{ $artwork->title }}" style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ Str::limit($artwork->title, 25) }}</h5>
                            <p class="card-text small text-muted mb-1">by <a href="{{ route('creator.profile', $artwork->user) }}">{{ $artwork->user->display_name ?? $artwork->user->name }}</a></p>
                        </div>
                        
                        <div class="card-footer bg-light d-flex justify-content-between">
                            {{-- Link untuk melihat detail karya di halaman publik --}}
                            <a href="{{ route('artworks.show', $artwork) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye-fill"></i> Lihat Karya
                            </a>
                            
                            {{-- Opsi Hapus Submission (Tolak Karya) --}}
                            <form action="{{ route('curator.submissions.destroy', $submission) }}" method="POST" onsubmit="return confirm('Tolak submission ini? Karya akan dihapus dari daftar challenge.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Mengasumsikan Anda menggunakan paginate() di controller Anda. Jika tidak, hapus links() --}}
        {{-- <div class="mt-4 d-flex justify-content-center">
            {{ $submissions->links() }}
        </div> --}}
    @endif
@endsection