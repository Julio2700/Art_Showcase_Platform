<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category; // Diperlukan untuk form create/edit
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    /**
     * List Artworks: Menampilkan daftar karya yang diunggah oleh Member.
     */
    public function index(): View
    {
        $artworks = Artwork::where('user_id', Auth::id())->latest()->paginate(10);
        return view('member.artworks.index', compact('artworks'));
    }

    /**
     * Create Artwork: Menampilkan formulir tambah karya.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('member.artworks.create', compact('categories'));

        // 💡 Pastikan ini memuat data dari database
        $categories = Category::all(); 
        
        return view('member.artworks.create', compact('categories'));
    }

    /**
     * Store Artwork: Menyimpan karya baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string', // Akan diubah menjadi array/json
            'file_upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Maks 5MB
        ]);
        
        // 1. Handle File Upload (Simpan gambar)
        $path = $request->file('file_upload')->store('artworks', 'public');
        
        // 2. Siapkan data untuk disimpan
        $artworkData = [
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $path,
            'tags' => explode(',', $validated['tags'] ?? ''), // Konversi tags string ke array
        ];

        Artwork::create($artworkData);

        return redirect()->route('member.artworks.index')
                         ->with('success', 'Karya seni berhasil diunggah!');
    }

    // SHOW: Tidak wajib di Dashboard Member, bisa redirect ke halaman publik
    public function show(Artwork $artwork): RedirectResponse
    {
        return redirect()->route('artworks.show', $artwork); // Arahkan ke halaman detail publik
    }


    /**
     * Edit Artwork: Menampilkan formulir edit.
     */
    public function edit(Artwork $artwork): View
    {
        // Otorisasi: Hanya kreator yang boleh mengedit
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit karya ini.');
        }

        $categories = Category::all();
        $artwork->tags = implode(', ', $artwork->tags ?? []); // Kembalikan tags ke string untuk form

        return view('member.artworks.edit', compact('artwork', 'categories'));
    }

    /**
     * Update Artwork: Memperbarui karya.
     */
    public function update(Request $request, Artwork $artwork): RedirectResponse
    {
        // Otorisasi: Hanya kreator yang boleh mengupdate
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memperbarui karya ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'file_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $artworkData = [
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'tags' => explode(',', $validated['tags'] ?? ''),
        ];
        
        // Handle File Update
        if ($request->hasFile('file_upload')) {
            // Hapus file lama
            Storage::disk('public')->delete($artwork->file_path);
            
            // Upload file baru
            $path = $request->file('file_upload')->store('artworks', 'public');
            $artworkData['file_path'] = $path;
        }

        $artwork->update($artworkData);

        return redirect()->route('member.artworks.index')
                         ->with('success', 'Karya seni berhasil diperbarui.');
    }

    /**
     * Delete Artwork: Menghapus karya.
     */
    public function destroy(Artwork $artwork): RedirectResponse
    {
        // Otorisasi: Hanya kreator yang boleh menghapus
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus karya ini.');
        }

        // Hapus file fisik
        Storage::disk('public')->delete($artwork->file_path);
        
        // Hapus record. Relasi cascade akan menghapus Like, Comment, Report, Submission.
        $artwork->delete();

        return redirect()->route('member.artworks.index')
                         ->with('success', 'Karya seni berhasil dihapus.');
    }

    
}