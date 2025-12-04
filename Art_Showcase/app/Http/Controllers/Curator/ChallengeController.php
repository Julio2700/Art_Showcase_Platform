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
    public function create(): View
    {
        return view('curator.challenges.create');
    }

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

        $path = $request->file('banner_path')->store('challenges/banners', 'public');

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

    public function index(): View
    {
        $curatorId = Auth::id();
        
        $challenges = Challenge::where('curator_id', $curatorId)
                               ->withCount('submissions')
                               ->latest()
                               ->paginate(10);

        return view('curator.challenges.index', compact('challenges'));
    }


    public function edit(Challenge $challenge): View
    {
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit challenge ini.');
        }
        
        if ($challenge->ends_at->isPast()) {
            return redirect()->route('curator.challenges.index')
                             ->with('error', 'Challenge yang sudah berakhir tidak dapat diedit.');
        }

        return view('curator.challenges.edit', compact('challenge'));
    }


    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memperbarui challenge ini.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date|after_or_equal:today',
            'ends_at' => 'required|date|after:starts_at',
            'banner_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072', 
        ]);

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
        ];

        if ($request->hasFile('banner_path')) {

            if ($challenge->banner_path) {
                Storage::disk('public')->delete($challenge->banner_path);
            }

            $path = $request->file('banner_path')->store('challenges/banners', 'public');
            $updateData['banner_path'] = $path;
        }

        $challenge->update($updateData);

        return redirect()->route('curator.challenges.index')
                         ->with('success', "Challenge '{$challenge->title}' berhasil diperbarui.");
    }


    public function destroy(Challenge $challenge): RedirectResponse
    {
        // 1. Otorisasi
        if ($challenge->curator_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus challenge ini.');
        }
        
        // 💡 PERBAIKAN: HAPUS SEMUA SUBMISSION TERKAIT DAHULU SEBELUM MENGHAPUS CHALLENGE
        if ($challenge->submissions()->exists()) {
            // Hapus semua submissions terkait (Wajib jika tidak ada onDelete('cascade') di migrasi Submission)
            $challenge->submissions()->delete(); 
        }

        // 3. Hapus file fisik (banner)
        if ($challenge->banner_path) {
            Storage::disk('public')->delete($challenge->banner_path);
        }
        
        // 4. Hapus record Challenge
        $challenge->delete();

        return redirect()->route('curator.challenges.index')
                         ->with('success', "Challenge '{$challenge->title}' berhasil dihapus.");
    }

    public function show(Challenge $challenge): View
    {

        $submissions = $challenge->submissions()
                                 ->with(['artwork.user']) 
                                 ->paginate(20);
                                 
        $is_over = $challenge->ends_at->isPast();
        
        $winners = $challenge->submissions()
                             ->where('is_winner', true)
                             ->with('artwork.user') 
                             ->orderBy('placement')
                             ->get();

        return view('public.challenge.show', compact('challenge', 'submissions', 'is_over', 'winners'));
    }
}