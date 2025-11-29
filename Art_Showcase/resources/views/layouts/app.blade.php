<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Showcase Platform - @yield('title')</title>
    {{-- Menggunakan Placeholder Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Vite CSS untuk styling front-end --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

</head>
<body>
    {{-- Navigasi menggunakan komponen Breeze (navigation.blade.php) --}}
    @include('layouts.navigation')

    {{-- KONTEN UTAMA --}}
    <main class="container py-4">
        {{-- Flash Messages (Pesan Sukses/Error) --}}
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
        
        {{-- PERBAIKAN KRITIS: Mengganti {{ $slot }} dengan @yield('content') --}}
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