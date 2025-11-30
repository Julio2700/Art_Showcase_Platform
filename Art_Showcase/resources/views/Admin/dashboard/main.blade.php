<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Pending Curator</h5>
                <p class="card-text display-4">{{ \App\Models\User::where('role', 'curator')->where('is_approved', false)->count() }}</p>
                <a href="{{ route('admin.users.index', ['role_filter' => 'curator']) }}" class="btn btn-sm btn-dark">Tinjau Sekarang <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Laporan Pending</h5>
                <p class="card-text display-4">{{ \App\Models\Report::where('status', 'pending')->count() }}</p>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-light text-danger">Moderasi <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total Karya</h5>
                <p class="card-text display-4">{{ \App\Models\Artwork::count() }}</p>
                <p class="mb-0 small">Karya terunggah di platform.</p>
            </div>
        </div>
    </div>
</div>

<h3 class="mt-4">Akses Cepat Admin</h3>
<div class="list-group">
    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">
        <i class="bi bi-tags-fill me-2"></i> Manajemen Kategori
    </a>
    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
        <i class="bi bi-people-fill me-2"></i> Manajemen User (Member, Curator, Admin)
    </a>
</div>