@extends('layouts.app') 

@section('title', 'Dashboard')

@section('content')
    <h1 class="mb-4">Dashboard Utama</h1>

    @if (Auth::user()->role === 'admin')
        {{-- Redirect ke halaman spesifik Admin di Controller --}}
        @include('admin.dashboard.main') 
        
    @elseif (Auth::user()->role === 'curator')
        @if (!Auth::user()->is_approved)
            @include('curator.dashboard.pending')
        @else
            @include('curator.dashboard.main')
        @endif
        
    @elseif (Auth::user()->role === 'member')
        @include('member.dashboard.main')
        
    @else
        <div class="alert alert-warning">Role Anda tidak dikenali. Silakan hubungi Admin.</div>
    @endif
    
@endsection 