@extends('layouts.app')

@section('title', 'Admin - Moderasi Laporan')

@section('content')
    <h1 class="mb-4">Moderasi Laporan</h1>

    {{-- ... (Filter Navigasi Anda) ... --}}

    @if ($reports->isEmpty())
        <div class="alert alert-info text-center">Tidak ada laporan yang perlu ditinjau saat ini.</div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Karya Seni (Artwork)</th>
                            <th>Detail Laporan</th>
                            <th>Aksi Moderasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            @php
                                $artwork = $report->artwork;
                                $user = $report->user;
                            @endphp
                            <tr class="align-top"> {{-- align-top agar konten panjang tidak mengganggu --}}
                                <td>{{ $report->id }}</td>
                                
                                {{-- 💡 KOLOM 2: PREVIEW KARYA (Lebih Besar) --}}
                                <td style="width: 250px;">
                                    @if ($artwork)
                                        {{-- Container untuk gambar yang lebih besar --}}
                                        <div style="width: 100%; max-width: 150px; height: auto; margin-bottom: 8px;">
                                            <img src="{{ $artwork->file_path ? asset('storage/' . $artwork->file_path) : 'placeholder_path' }}" 
                                                class="img-thumbnail" 
                                                style="max-width: 100%; height: auto; object-fit: contain;" 
                                                alt="Artwork Preview">
                                        </div>
                                        
                                        <div>
                                            <strong class="text-primary">{{ Str::limit($artwork->title, 30) }}</strong><br>
                                            <small class="text-muted">Kreator: {{ $artwork->user?->display_name ?? 'Pengguna Dihapus' }}</small>
                                        </div>
                                    @else
                                        <span class="text-danger">Karya Dihapus</span>
                                    @endif
                                </td>

                                {{-- KOLOM 3: DETAIL LAPORAN --}}
                                <td>
                                    <p class="mb-1 small"><strong>Dilaporkan oleh:</strong> {{ $user?->name ?? 'Pengguna Dihapus' }}</p>
                                    <p class="mb-1"><strong>Alasan:</strong></p>
                                    <div class="alert alert-light border p-2 mb-2">
                                        {{ $report->reason }}
                                    </div>
                                    <p class="text-muted small">Waktu: {{ $report->created_at->diffForHumans() }}</p>
                                    <a href="{{ route('artworks.show', $artwork) }}" target="_blank" class="small fw-bold">Lihat Halaman Publik</a>
                                </td>

                                {{-- KOLOM 4: AKSI MODERASI --}}
                                <td style="width: 180px;">
                                    {{-- 1. Tombol HAPUS KARYA & SETUJUI LAPORAN (Taken Down) --}}
                                    <form action="{{ route('admin.reports.update', $report) }}" method="POST" onsubmit="return confirm('Hapus permanen karya ini dan setujui laporan?')" class="mb-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="taken_down">
                                        <button type="submit" class="btn btn-danger btn-sm w-100">Hapus Karya & Setujui</button>
                                    </form>

                                    {{-- 2. Tombol ABAIKAN & SELESAIKAN (Dismissed) --}}
                                    <form action="{{ route('admin.reports.update', $report) }}" method="POST" onsubmit="return confirm('Tandai sebagai selesai dan abaikan laporan?')" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="dismissed">
                                        <button type="submit" class="btn btn-success btn-sm w-100">Abaikan & Selesaikan</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {{ $reports->links() }}
        </div>
    @endif
@endsection