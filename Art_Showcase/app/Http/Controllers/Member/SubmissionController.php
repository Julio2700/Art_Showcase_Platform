<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Artwork;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Menampilkan form pemilihan Artwork untuk disubmit ke Challenge tertentu.
     */
    public function create(Challenge $challenge): View
    {
        if (Auth::user()->role === 'curator') {
            abort(403, 'Akses Ditolak: Curator tidak dapat berpartisipasi dalam challenge.');
        }
        // Pastikan Challenge masih aktif
        if ($challenge->ends_at->isPast()) {
            return redirect()->route('challenges.show', $challenge)->with('error', 'Challenge sudah berakhir.');
        }

        // Ambil Artwork yang dimiliki oleh user yang sedang login
        $artworks = Artwork::where('user_id', Auth::id())->latest()->get();

        return view('member.submissions.create', compact('challenge', 'artworks'));
    }

    /**
     * Menyimpan submission (menghubungkan Artwork ke Challenge).
     */
    public function store(Request $request, Challenge $challenge): RedirectResponse
    {
        if (Auth::user()->role === 'curator') {
            return back()->with('error', 'Akses Ditolak: Curator tidak diizinkan untuk mengunggah karya ke challenge.');
        }

        $validated = $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
        ]);
        
        // 1. Validasi kepemilikan Artwork
        $artwork = Artwork::where('id', $validated['artwork_id'])
                          ->where('user_id', Auth::id())
                          ->firstOrFail();
                          
        // 2. Cek apakah Artwork sudah pernah disubmit ke Challenge ini
        $existing = Submission::where('challenge_id', $challenge->id)
                              ->where('artwork_id', $artwork->id)
                              ->exists();
                              
        if ($existing) {
            return back()->with('error', 'Karya ini sudah disubmit ke challenge yang sama.');
        }

        // 3. Buat Submission
        Submission::create([
            'challenge_id' => $challenge->id,
            'artwork_id' => $artwork->id,
        ]);

        return redirect()->route('challenges.show', $challenge)
                         ->with('success', 'Karya Anda berhasil disubmit ke challenge!');
    }
}