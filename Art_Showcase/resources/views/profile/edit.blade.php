@extends('layouts.app') 

@section('title', 'Pengaturan Profil')

@section('content')
    <h2 class="mb-4">Pengaturan Profil</h2>

    <div class="card p-4 mb-4">
        {{-- Section: Update Profile Information --}}
        @include('profile.partials.update-profile-information-form')
        
        <p class="mt-3">Role Anda saat ini: <span class="badge bg-secondary">{{ $user->role }}</span></p>
        @if ($user->role === 'curator')
            <p>Status Persetujuan: 
                <span class="badge {{ $user->is_approved ? 'bg-success' : 'bg-warning' }}">
                    {{ $user->is_approved ? 'Disetujui' : 'Pending' }}
                </span>
            </p>
        @endif
    </div>

    <div class="card p-4 mb-4">
        {{-- Section: Update Password --}}
        @include('profile.partials.update-password-form')
    </div>

    <div class="card p-4">
        {{-- Section: Delete Account --}}
        @include('profile.partials.delete-user-form')
    </div>
@endsection