@extends('layouts.app')

@section('title', 'Curator - Kelola Challenge')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manajemen Challenge Anda</h1>
        <a href="{{ route('curator.challenges.create') }}" class="btn btn-success"><i class="bi bi-plus-circle-fill me-2"></i> Buat Challenge Baru</a>
    </div>

    @if ($challenges->isEmpty())
        <div class="alert alert-info text-center p-4">
            <p class="h4">Anda belum membuat challenge apa pun.</p>
            <a href="{{ route('curator.challenges.create') }}" class="btn btn-primary mt-3">Mulai Challenge Pertama</a>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Periode</th>
                            <th>Submissions</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($challenges as $challenge)
                            @php
                                $isOngoing = now()->between($challenge->starts_at, $challenge->ends_at);
                                $isOver = now()->greaterThan($challenge->ends_at);
                                $isUpcoming = now()->lessThan($challenge->starts_at); // Pengecekan Upcoming
                                $hasWinners = $challenge->submissions()->where('is_winner', true)->exists();
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $challenge->title }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($challenge->description, 50) }}</small>
                                </td>
                                <td>
                                    Mulai: {{ $challenge->starts_at->format('d M Y') }}<br>
                                    Deadline: {{ $challenge->ends_at->format('d M Y') }}
                                </td>
                                <td><span class="badge bg-secondary">{{ $challenge->submissions_count }} Submissions</span></td>
                                <td>
                                    @if ($isOngoing)
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif ($isOver && $hasWinners)
                                        <span class="badge bg-primary">Selesai (Pemenang Diumumkan)</span>
                                    @elseif ($isOver && !$hasWinners)
                                        <span class="badge bg-danger">Selesai, Perlu Pemenang</span>
                                    @elseif ($isUpcoming)
                                        <span class="badge bg-warning">Akan Datang</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Tombol Review/Pilih Pemenang --}}
                                    @if ($isOver && !$hasWinners)
                                        <a href="{{ route('curator.submissions.index', $challenge) }}" class="btn btn-sm btn-danger me-1">Pilih Pemenang</a>
                                    @else
                                        <a href="{{ route('curator.submissions.index', $challenge) }}" class="btn btn-sm btn-secondary me-1">Lihat Submissions</a>
                                    @endif

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('curator.challenges.edit', $challenge) }}" class="btn btn-sm btn-outline-primary me-1" {{ $isOver ? 'disabled' : '' }}>Edit</a>
                                    
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('curator.challenges.destroy', $challenge) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus challenge ini? Submissions akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {{ $challenges->links() }}
        </div>
    @endif
@endsection