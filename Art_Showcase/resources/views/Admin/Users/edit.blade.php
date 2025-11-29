@extends('layouts.app')

@section('title', 'Admin - Edit User: ' . $user->name)

@section('content')
    <h1 class="mb-4">Edit Profil User: {{ $user->name }}</h1>
    
    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-4">
                <label for="role" class="form-label">Ubah Role <span class="text-danger">*</span></label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="member" {{ old('role', $user->role) == 'member' ? 'selected' : '' }}>Member</option>
                    <option value="curator" {{ old('role', $user->role) == 'curator' ? 'selected' : '' }}>Curator</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            {{-- Bagian Khusus Curator --}}
            @if ($user->role === 'curator')
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="1" id="is_approved" name="is_approved" {{ old('is_approved', $user->is_approved) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_approved">
                        Setujui sebagai Curator (Status `is_approved`)
                    </label>
                </div>
            @endif

            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection