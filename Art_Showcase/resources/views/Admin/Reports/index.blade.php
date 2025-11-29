@extends('layouts.app')

@section('title', 'Admin - Moderation Queue')

@section('content')
    <h1 class="mb-4">Moderation Queue (Laporan Karya)</h1>
    
    <div class="d-flex mb-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Manajemen User
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-{{ request('status') == 'pending' || !request('status') ? 'danger' : 'outline-danger' }}">
            Laporan Pending
        </a>
    </div>

    @if ($reports->isEmpty())
        <div class="alert alert-success text-center">Tidak ada laporan **pending** yang perlu ditinjau. Moderasi bersih!</div>
    @else
        <p class="lead text-danger">Total {{ $reports->total() }} Laporan Masuk.</p>
        
        <div class="row row-cols-1 g-4">
            @foreach ($reports as $report)
                <div class="col">
                    <div class="card shadow-sm border-danger">
                        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                            <strong>Laporan Karya: {{ $report->artwork->title }}</strong>
                            <span class="badge bg-light text-danger">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Preview Karya --}}
                                <div class="col-md-3 text-center">
                                    <img src="{{ asset('storage/' . $report->artwork->file_path) }}" class="img-thumbnail mb-2" style="height: 150px; object-fit: cover;" alt="Artwork">
                                    <p class="mb-0 small fw-bold"><a href="{{ route('artworks.show', $report->artwork) }}" target="_blank">Lihat Karya</a></p>
                                    <p class="small text-muted">Kreator: {{ $report->artwork->user->name }}</p>
                                </div>
                                
                                {{-- Detail Laporan & Aksi --}}
                                <div class="col-md-9">
                                    <p><strong>Dilaporkan oleh:</strong> {{ $report->user->name }} (ID: {{ $report->user->id }})</p>
                                    <p><strong>Alasan:</strong></p>
                                    <div class="alert alert-light border p-2">
                                        {{ $report->reason }}
                                    </div>

                                    <div class="mt-3">
                                        <p><strong>Aksi Moderasi:</strong></p>
                                        
                                        {{-- Aksi 1: Take Down (Update status report ke 'taken_down') --}}
                                        <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('APAKAH ANDA YAKIN? Tindakan ini akan menghapus karya seni terkait.')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="taken_down">
                                            <button type="submit" class="btn btn-sm btn-danger me-2">
                                                <i class="bi bi-x-octagon-fill"></i> Hapus Karya & Setujui
                                            </button>
                                        </form>

                                        {{-- Aksi 2: Dismiss (Update status report ke 'dismissed') --}}
                                        <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai laporan ini sebagai ditolak/selesai?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="dismissed">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle-fill"></i> Abaikan & Selesaikan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $reports->links() }}
        </div>
    @endif
@endsection