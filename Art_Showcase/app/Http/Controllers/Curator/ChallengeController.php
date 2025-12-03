<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    // ... (index, show, edit, update, destroy methods) ...

    /**
     * CREATE: Menampilkan formulir tambah challenge.
     */
    public function create(): View
    {
        return view('curator.challenges.create');
    }

    /**
     * STORE: Menyimpan challenge baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date|after_or_equal:today',
            'ends_at' => 'required|date|after:starts_at',
            'banner_path' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072', // Maks 3MB
        ]);

        $startsAt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['starts_at'], config('app.timezone'));
        $endsAt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['ends_at'], config('app.timezone'));

        // 1. Handle File Upload (Simpan banner)
        $path = $request->file('banner_path')->store('challenges/banners', 'public');

        // 2. Buat Challenge
        Challenge::create([
            'curator_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'starts_at' => $startsAt, 
            'ends_at' => $endsAt,     
            'banner_path' => $path,
        ]);

        return redirect()->route('curator.challenges.index')
                         ->with('success', 'Challenge baru berhasil dibuat dan aktif!');
    }

    /**
     * INDEX: Menampilkan daftar semua challenge yang dibuat oleh Curator yang login.
     */
    public function index(): View
    {
        $curatorId = Auth::id();
        
        // Memuat challenge milik curator saat ini, dan menghitung submissions untuk setiap challenge
        $challenges = Challenge::where('curator_id', $curatorId)
                               ->withCount('submissions')
                               ->latest()
                               ->paginate(10);
                               
        return view('curator.challenges.index', compact('challenges'));
    }

    /**
     * EDIT: Menampilkan formulir edit challenge.
     * Route: GET /curator/challenges/{challenge}/edit (curator.challenges.edit)
     */
    public function edit(Challenge $challenge): View
    {
        // Otorisasi: Hanya Curator pembuat yang bisa mengedit
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit challenge ini.');
        }
        
        // Cek apakah challenge sudah berakhir
        if ($challenge->ends_at->isPast()) {
            return redirect()->route('curator.challenges.index')
                             ->with('error', 'Challenge yang sudah berakhir tidak dapat diedit.');
        }

        return view('curator.challenges.edit', compact('challenge'));
    }

    /**
     * UPDATE: Memperbarui challenge yang sudah ada.
     * Route: PUT/PATCH /curator/challenges/{challenge} (curator.challenges.update)
     */
    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        // Otorisasi: Hanya Curator pembuat
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memperbarui challenge ini.');
        }
        
        // Validasi
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date|after_or_equal:today',
            'ends_at' => 'required|date|after:starts_at',
            'banner_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072', // Opsional saat update
        ]);

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
        ];

        // Handle File Update
        if ($request->hasFile('banner_path')) {
            // Hapus file lama jika ada
            if ($challenge->banner_path) {
                Storage::disk('public')->delete($challenge->banner_path);
            }
            // Upload file baru
            $path = $request->file('banner_path')->store('challenges/banners', 'public');
            $updateData['banner_path'] = $path;
        }

        $challenge->update($updateData);

        return redirect()->route('curator.challenges.index')
                         ->with('success', "Challenge '{$challenge->title}' berhasil diperbarui.");
    }

    /**
     * DESTROY: Menghapus challenge.
     * Route: DELETE /curator/challenges/{challenge} (curator.challenges.destroy)
     */
    public function destroy(Challenge $challenge): RedirectResponse
    {
        // 1. Otorisasi: Hanya Curator pembuat yang bisa menghapus
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus challenge ini.');
        }
        
        // 2. Guardrail: Cek apakah sudah ada submissions
        if ($challenge->submissions()->exists()) {
            return back()->with('error', 'Gagal: Challenge ini tidak dapat dihapus karena sudah memiliki submissions.');
        }

        // 3. Hapus file fisik (banner)
        if ($challenge->banner_path) {
            Storage::disk('public')->delete($challenge->banner_path);
        }
        
        // 4. Hapus record Challenge (Relasi cascade akan menghapus submissions jika ada)
        $challenge->delete();

        return redirect()->route('curator.challenges.index')
                         ->with('success', "Challenge '{$challenge->title}' berhasil dihapus.");
    }
}