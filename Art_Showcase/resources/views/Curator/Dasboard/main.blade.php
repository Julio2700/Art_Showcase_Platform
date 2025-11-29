<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Challenge Aktif Anda</h5>
                <p class="card-text display-4">{{ \App\Models\Challenge::where('curator_id', Auth::id())->where('ends_at', '>', now())->count() }}</p>
                <a href="{{ route('curator.challenges.index') }}" class="btn btn-sm btn-light text-info">Kelola Challenge <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card bg-secondary text-white shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total Submissions Masuk</h5>
                {{-- Ini memerlukan Query agak kompleks, bisa dihitung di Controller atau di Model --}}
                <p class="card-text display-4">{{ \App\Models\Challenge::where('curator_id', Auth::id())->withCount('submissions')->get()->sum('submissions_count') }}</p>
                <p class="mb-0 small">Total karya yang disubmit ke challenge Anda.</p>
            </div>
        </div>
    </div>
</div>
<h3 class="mt-4">Akses Curator</h3>
<div class="list-group">
    <a href="{{ route('curator.challenges.index') }}" class="list-group-item list-group-item-action">
        <i class="bi bi-trophy-fill me-2"></i> Kelola & Buat Challenge Baru
    </a>
</div>