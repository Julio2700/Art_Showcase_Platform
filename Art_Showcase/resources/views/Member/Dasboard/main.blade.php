<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Karya Seni Diunggah</h5>
                <p class="card-text display-4">{{ \App\Models\Artwork::where('user_id', Auth::id())->count() }}</p>
                <a href="{{ route('member.artworks.index') }}" class="btn btn-sm btn-light text-primary">Kelola Karya <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Karya di Favoritkan</h5>
                <p class="card-text display-4">{{ \App\Models\Favorite::where('user_id', Auth::id())->count() }}</p>
                {{-- Asumsi ada route untuk Favorite List --}}
                <a href="{{ route('member.favorites.index') }}" class="btn btn-sm btn-dark">Lihat Favorit <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-light shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Aksi Cepat</h5>
                <a href="{{ route('member.artworks.create') }}" class="btn btn-success w-100 mb-2">
                    <i class="bi bi-cloud-upload-fill"></i> Unggah Karya Baru
                </a>
                <a href="{{ route('member.artworks.index') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Galeri Pribadi
                </a>
            </div>
        </div>
    </div>
</div>