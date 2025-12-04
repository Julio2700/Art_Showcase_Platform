<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Artwork;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
        // Otorisasi: Hanya Challenge Curator yang bisa menolak submission
        if ($submission->challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menolak submission ini.');
        }

        $submission->delete();

        return back()->with('success', 'Submission berhasil ditolak dan dihapus dari challenge.');
    }

    public function showWinnersForm(Challenge $challenge): View
    {
        // Otorisasi: Hanya Curator pemilik challenge yang bisa memilih pemenang
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memilih pemenang untuk challenge ini.');
        }

        // Memuat submissions yang belum ditetapkan sebagai pemenang
        $submissions = $challenge->submissions()
                                 ->with('artwork.user')
                                 ->get();

        return view('curator.challenges.winners', compact('challenge', 'submissions'));
    }

    public function storeWinners(Request $request, Challenge $challenge): RedirectResponse
    {
        // 1. Otorisasi
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menetapkan pemenang untuk challenge ini.');
        }

        // 2. Validasi ID Submission dan mencegah duplikasi
        $validated = $request->validate([
            'winner_1' => ['required', 'exists:submissions,id'],
            'winner_2' => ['required', 'exists:submissions,id', 'different:winner_1'],
            'winner_3' => ['required', 'exists:submissions,id', 'different:winner_1', 'different:winner_2'],
        ]);

        // 3. Pengecekan Kritis: Challenge harus sudah berakhir
        if (now()->lessThan($challenge->ends_at)) {
             return back()->with('error', 'Challenge belum berakhir. Pemenang hanya dapat ditetapkan setelah deadline.');
        }

        // 4. Update Database
        DB::transaction(function () use ($validated, $challenge) {
            
            // 4a. Reset semua status pemenang lama (penting!)
            // Hanya reset submissions yang terkait dengan challenge ini
            Submission::where('challenge_id', $challenge->id)->update(['is_winner' => false, 'placement' => null]);
            
            // 4b. Tetapkan Juara 1, 2, 3
            Submission::where('id', $validated['winner_1'])->update(['is_winner' => true, 'placement' => 1]);
            
            Submission::where('id', $validated['winner_2'])->update(['is_winner' => true, 'placement' => 2]);
            
            Submission::where('id', $validated['winner_3'])->update(['is_winner' => true, 'placement' => 3]);
        });

        return redirect()->route('curator.challenges.index')
                         ->with('success', 'Pemenang Challenge telah berhasil ditetapkan!');
    }

    

}