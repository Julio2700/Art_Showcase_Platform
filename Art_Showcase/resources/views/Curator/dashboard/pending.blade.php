{{-- Hapus @extends dan @section dan ganti dengan Blade Component <x-guest-layout> --}}
<x-guest-layout> 
    
    {{-- Anda bisa menambahkan judul di sini, tetapi konten utama harus langsung di dalam layout component --}}
    <h2 class="text-2xl font-bold mb-4 text-center">Menunggu Persetujuan</h2>

    <div class="card p-4 text-center">
        <i class="bi bi-clock-history h1 text-warning"></i>
        <h2 class="mt-3">Akun Anda sedang ditinjau.</h2>
        <p class="text-muted">Terima kasih telah mendaftar sebagai Curator. Admin sedang meninjau permintaan Anda. Anda akan mendapatkan akses ke Dashboard Curator setelah disetujui.</p>
        
        {{-- Tombol Delete Account jika ditolak (Syarat opsional, perlu logika di Controller) --}}
        @if (false) {{-- Placeholder logic jika status ditolak --}}
            <hr>
            <p class="text-danger">Sayangnya, permintaan Anda ditolak.</p>
            <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger mt-2">Hapus Akun</button>
            </form>
        @endif
        
        <div class="mt-4">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-dark">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    
</x-guest-layout>