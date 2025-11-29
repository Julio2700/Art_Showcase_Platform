<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    // Hanya menggunakan method yang dibutuhkan untuk meninjau dan memilih pemenang
    
    // READ: Menampilkan galeri submission untuk challenge tertentu
    // URL: /curator/submissions/{challenge}
    public function index(Challenge $challenge): View
    {
        // Otorisasi: Memastikan Challenge ini milik Curator yang sedang login
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        $submissions = $challenge->submissions()
                                ->with('artwork.user') // Load karya seni dan kreatornya
                                ->latest()
                                ->get();
                                
        // Logic untuk menampilkan halaman tinjauan submission
        return view('curator.submissions.index', compact('challenge', 'submissions'));
    }

    /**
     * Memilih atau menghapus status pemenang (Select Winners).
     * URL: /curator/submissions/{submission}/set-winner
     */
    public function setWinner(Request $request, Submission $submission): RedirectResponse
    {
        // Otorisasi: Memastikan Challenge ini milik Curator yang sedang login
        if ($submission->challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memilih pemenang untuk challenge ini.');
        }
        
        // Pengecekan: Pastikan challenge sudah berakhir
        if ($submission->challenge->ends_at->isFuture()) {
             return back()->with('error', 'Gagal: Pemenang hanya bisa dipilih setelah deadline challenge.');
        }
        
        $validated = $request->validate([
            'placement' => 'required|integer|min:1|max:3', // Juara 1, 2, atau 3
        ]);
        
        // Memastikan tidak ada juara ganda dengan placement yang sama di challenge ini (Opsional)
        $existingWinner = $submission->challenge->submissions()
                                            ->where('placement', $validated['placement'])
                                            ->first();
        if ($existingWinner && $existingWinner->id !== $submission->id) {
             return back()->with('error', "Juara {$validated['placement']} sudah diisi oleh karya lain.");
        }


        // Update submission menjadi pemenang
        $submission->update([
            'is_winner' => true,
            'placement' => $validated['placement'],
        ]);

        return back()->with('success', "Karya '{$submission->artwork->title}' berhasil ditetapkan sebagai Juara {$validated['placement']}!");
    }

    /**
     * Menghapus karya dari daftar Submission Challenge (Memvalidasi/Menolak Submission).
     * URL: /curator/submissions/{submission} (DELETE)
     */
    public function destroy(Submission $submission): RedirectResponse
    {
        // Otorisasi: Memastikan Challenge ini milik Curator yang sedang login
        if ($submission->challenge->curator_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }
        
        $submission->delete();
        
        return back()->with('success', 'Submission berhasil dihapus dari Challenge.');
    }
}