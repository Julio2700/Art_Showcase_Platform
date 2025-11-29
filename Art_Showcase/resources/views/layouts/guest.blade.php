<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Showcase Platform - @yield('title')</title>
    {{-- Menggunakan Placeholder Bootstrap untuk styling dasar --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Ikon (opsional) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
</head>
<body>
    {{-- HEADER/NAVIGASI --}}
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ route('homepage') }}">Art Showcase</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ route('artworks.catalog') }}">Galeri</a></li>
                        {{-- Navigasi Dashboard Berdasarkan Role --}}
                        @auth
                            @if (Auth::user()->role === 'admin')
                                <li class="nav-item"><a class="nav-link text-warning" href="{{ route('admin.users.index') }}"><i class="bi bi-gear-fill"></i> Admin</a></li>
                            @elseif (Auth::user()->role === 'curator' && Auth::user()->is_approved)
                                <li class="nav-item"><a class="nav-link text-info" href="{{ route('curator.challenges.index') }}"><i class="bi bi-trophy-fill"></i> Curator</a></li>
                            @elseif (Auth::user()->role === 'member')
                                <li class="nav-item"><a class="nav-link text-success" href="{{ route('member.artworks.index') }}"><i class="bi bi-upload"></i> Member</a></li>
                            @endif
                        @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item"><a class="btn btn-outline-light me-2" href="{{ route('login') }}">Login</a></li>
                            <li class="nav-item"><a class="btn btn-primary" href="{{ route('register') }}">Register</a></li>
                        @else
                            {{-- Dropdown Profil --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }} ({{ Auth::user()->role }})
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Pengaturan Profil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    {{-- KONTEN UTAMA --}}
    <main class="container py-4">
        {{-- Flash Messages (Pesan Sukses/Error dari Controller) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        {{-- Konten Dinamis dari View Anak --}}
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-light mt-auto py-3">
        <div class="container text-center">
            <p class="mb-0">Hak Cipta © {{ date('Y') }} Art Showcase Platform</p>
        </div>
    </footer>

    {{-- Skrip JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>