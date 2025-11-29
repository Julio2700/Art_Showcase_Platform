<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    /**
     * Menampilkan daftar semua challenge yang dibuat oleh Curator yang sedang login.
     */
    public function index(): View
    {
        $challenges = Challenge::where('curator_id', Auth::id())
                            ->withCount('submissions')
                            ->latest()
                            ->paginate(10);
                            
        return view('curator.challenges.index', compact('challenges'));
    }

    /**
     * Menampilkan formulir untuk membuat challenge baru.
     */
    public function create(): View
    {
        return view('curator.challenges.create');
    }

    /**
     * Menyimpan challenge baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date|after_or_equal:today',
            'ends_at' => 'required|date|after:starts_at',
            'banner_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Aturan dan Hadiah bisa digabung di description atau kolom terpisah jika perlu
        ]);
        
        // Handle File Upload (Banner)
        if ($request->hasFile('banner_path')) {
            $path = $request->file('banner_path')->store('challenges/banners', 'public');
            $validated['banner_path'] = $path;
        }

        // Set curator_id otomatis
        $validated['curator_id'] = Auth::id();

        Challenge::create($validated);

        return redirect()->route('curator.challenges.index')
                         ->with('success', 'Challenge berhasil dibuat.');
    }

    /**
     * Menampilkan detail challenge dan galeri submission.
     */
    public function show(Challenge $challenge): View
    {
        // Otorisasi: Hanya curator pembuat yang boleh melihat di dashboard mereka
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak melihat challenge ini.');
        }

        $submissions = $challenge->submissions()->with('artwork.user')->latest()->paginate(20);
        
        return view('curator.challenges.show', compact('challenge', 'submissions'));
    }

    /**
     * Menampilkan formulir untuk mengedit challenge.
     */
    public function edit(Challenge $challenge): View
    {
        // Otorisasi: Hanya curator pembuat yang boleh mengedit
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit challenge ini.');
        }
        return view('curator.challenges.edit', compact('challenge'));
    }

    /**
     * Memperbarui challenge di database.
     */
    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        // Otorisasi: Hanya curator pembuat yang boleh mengupdate
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memperbarui challenge ini.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date|after_or_equal:today',
            'ends_at' => 'required|date|after:starts_at',
            'banner_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Handle File Upload dan Hapus File Lama jika ada
        if ($request->hasFile('banner_path')) {
            // Logika Hapus File Lama (Opsional, perlu import Storage)
            // Storage::delete($challenge->banner_path);
            
            $path = $request->file('banner_path')->store('challenges/banners', 'public');
            $validated['banner_path'] = $path;
        }

        $challenge->update($validated);

        return redirect()->route('curator.challenges.index')
                         ->with('success', 'Challenge berhasil diperbarui.');
    }

    /**
     * Menghapus challenge dari database.
     */
    public function destroy(Challenge $challenge): RedirectResponse
    {
        // Otorisasi: Hanya curator pembuat yang boleh menghapus
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus challenge ini.');
        }
        
        // Pengecekan: Pastikan tidak ada submission aktif (Opsional)
        // Jika ada submissions, Challenge tidak boleh dihapus agar integritas data terjaga
        if ($challenge->submissions()->count() > 0) {
             return back()->with('error', 'Gagal: Challenge ini sudah memiliki submission.');
        }

        // Hapus file banner sebelum menghapus record
        // Storage::delete($challenge->banner_path); 

        $challenge->delete();

        return redirect()->route('curator.challenges.index')
                         ->with('success', 'Challenge berhasil dihapus.');
    }
}