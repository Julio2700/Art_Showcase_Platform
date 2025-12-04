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

    public function show(Report $report): View
    {
        $report->load(['user', 'artwork.user']);
        return view('admin.reports.show', compact('report'));
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['dismissed', 'taken_down'])],
        ]);

        $newStatus = $validated['status'];
        
        if ($newStatus === 'taken_down') {

            $artwork = Artwork::find($report->artwork_id);

            if ($artwork) {

                $artwork->delete();
                $message = "Laporan disetujui, dan karya seni '{$artwork->title}' telah dihapus.";
            } else {
                 $message = "Laporan disetujui, namun karya seni terkait tidak ditemukan.";
            }
        } else {

            $message = "Laporan berhasil ditolak.";
        }

        $report->update(['status' => $newStatus]);

        return redirect()->route('admin.reports.index', ['status' => 'pending'])
                         ->with('success', $message);
    }
    
}