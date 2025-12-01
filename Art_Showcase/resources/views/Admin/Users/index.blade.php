@extends('layouts.app')

@section('title', 'Admin - Manajemen Pengguna')

@section('content')
    <h1 class="mb-4">Manajemen Pengguna</h1>
    
    @php
        // Ambil jumlah count untuk badge filter (membantu mengatasi cache visual)
        $allCount = \App\Models\User::count();
        $pendingCount = \App\Models\User::where('role', 'curator')->where('is_approved', false)->count();
    @endphp

    {{-- Filter berdasarkan Status --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4>Daftar Semua Pengguna</h4>
        <div>
            <a href="{{ route('admin.users.index', ['status' => 'all']) }}" class="btn btn-{{ $status === 'all' ? 'primary' : 'outline-secondary' }} me-2">Semua ({{ $allCount }})</a>
            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="btn btn-{{ $status === 'pending' ? 'warning' : 'outline-warning' }} me-2">Pending Curator ({{ $pendingCount }})</a>
            <a href="{{ route('admin.users.index', ['status' => 'curator']) }}" class="btn btn-{{ $status === 'curator' ? 'info' : 'outline-info' }} me-2">Curator</a>
            <a href="{{ route('admin.users.index', ['status' => 'member']) }}" class="btn btn-{{ $status === 'member' ? 'success' : 'outline-success' }}">Member</a>
        </div>
    </div>

    @if ($users->isEmpty())
        <div class="alert alert-info text-center">Tidak ada pengguna yang ditemukan dengan filter ini.</div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama / Email</th>
                            <th>Role</th>
                            <th>Status Approval</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <strong>{{ $user->name }} ({{ $user->display_name }})</strong><br>
                                    <small>{{ $user->email }}</small>
                                </td>
                                <td><span class="badge bg-{{ $user->role === 'admin' ? 'dark' : ($user->role === 'curator' ? 'info' : 'success') }}">{{ ucfirst($user->role) }}</span></td>
                                
                                {{-- KOLOM 4: STATUS APPROVAL --}}
                                <td>
                                    @if ($user->role === 'curator')
                                        <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning' }}">
                                            {{ $user->is_approved ? 'Disetujui' : 'Pending' }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                
                                {{-- KOLOM 5: AKSI --}}
                                <td>
                                    @if ($user->role === 'curator' && !$user->is_approved)
                                        {{-- KONDISI 1: Curator Pending (Tombol Persetujuan) --}}
                                        
                                        {{-- 1. Tombol TERIMA (Route: admin.users.approve) --}}
                                        <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui akun Curator {{ $user->name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success me-2">Terima</button>
                                        </form>

                                        {{-- 2. Tombol TOLAK (Route: admin.users.destroy) --}}
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Tolak dan Hapus permanen akun Curator {{ $user->name }}? Aksi ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                        </form>

                                    @else
                                        {{-- KONDISI 2: Admin, Member, atau Curator yang Sudah Disetujui/Default --}}

                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                        
                                        {{-- Form Hapus --}}
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" {{ Auth::id() === $user->id ? 'disabled' : '' }}>Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    @endif
@endsection