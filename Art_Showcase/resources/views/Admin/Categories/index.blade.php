@extends('layout.master')

@section('title', 'Admin - Daftar Kategori')

@section('content')
    <h1 class="mb-4">Manajemen Kategori</h1>
    
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard Admin
        </a>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
        </a>
    </div>

    @if ($categories->isEmpty())
        <div class="alert alert-info text-center">Belum ada kategori yang ditambahkan.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Karya</th>
                        <th>Dibuat Pada</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->artworks_count }}</td>
                            <td>{{ $category->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                                
                                {{-- Form DELETE dengan konfirmasi --}}
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    {{-- Tombol Hapus dinonaktifkan jika ada karya terkait --}}
                                    <button type="submit" class="btn btn-sm btn-danger" {{ $category->artworks_count > 0 ? 'disabled' : '' }}>
                                        Hapus
                                    </button>
                                    @if ($category->artworks_count > 0)
                                        <span class="text-danger small ms-1">({{ $category->artworks_count }} Karya)</span>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    @endif
@endsection