<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    // Menampilkan daftar laporan (Queue)
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $reports = Report::query()
            ->with(['user:id,name', 'artwork.user:id,name,display_name,avatar_path']) 
            ->where('status', $status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact('reports', 'status'));
    }
    // Menampilkan detail laporan dan karya yang dilaporkan
    public function show(Report $report): View
    {
        $report->load(['user', 'artwork.user']); // Load pelapor, karya, dan kreator karya
        return view('admin.reports.show', compact('report'));
    }

    // Memproses keputusan laporan (Dismissed/Taken Down)
    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['dismissed', 'taken_down'])],
        ]);

        $newStatus = $validated['status'];
        
        // Cek jika status diubah menjadi 'taken_down' (Karya dihapus/disembunyikan)
        if ($newStatus === 'taken_down') {
            // Kita asumsikan ada kolom 'is_active' atau 'status' di tabel artworks.
            // Jika tidak ada, Anda perlu menambahkan kolom itu di migrasi artworks.
            $artwork = Artwork::find($report->artwork_id);

            if ($artwork) {
                // Contoh ideal: mengubah status artwork menjadi non-aktif/hidden
                // $artwork->update(['is_active' => false]);
                
                // Atau menghapus karyanya (sangat permanen)
                // $artwork->delete(); 
                
                // Untuk proyek ini, kita anggap karya disembunyikan/di-take down
                $artwork->delete(); // Jika artwork dihapus
                $message = "Laporan disetujui, dan karya seni '{$artwork->title}' telah dihapus.";
            } else {
                 $message = "Laporan disetujui, namun karya seni terkait tidak ditemukan.";
            }
        } else {
            // Status: Dismissed (Laporan ditolak/dikesampingkan)
            $message = "Laporan berhasil ditolak.";
        }

        // Perbarui status laporan
        $report->update(['status' => $newStatus]);

        return redirect()->route('admin.reports.index', ['status' => 'pending'])
                         ->with('success', $message);
    }
    
    // Admin tidak perlu membuat laporan baru atau menghapus permanen laporan
}