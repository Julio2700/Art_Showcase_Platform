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
    public function index(): View
    {
        $artworks = Artwork::where('user_id', Auth::id())->latest()->paginate(10);
        return view('member.artworks.index', compact('artworks'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('member.artworks.create', compact('categories'));

        $categories = Category::all(); 
        
        return view('member.artworks.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string', 
            'file_upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', 
        ]);
        
        $path = $request->file('file_upload')->store('artworks', 'public');
        
        $artworkData = [
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $path,
            'tags' => explode(',', $validated['tags'] ?? ''), 
        ];

        Artwork::create($artworkData);

        return redirect()->route('member.artworks.index')
                         ->with('success', 'Karya seni berhasil diunggah!');
    }

    public function show(Artwork $artwork): RedirectResponse
    {
        return redirect()->route('artworks.show', $artwork); 
    }


    public function edit(Artwork $artwork): View
    {
        // Otorisasi: Hanya kreator yang boleh mengedit
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit karya ini.');
        }

        $categories = Category::all();
        $artwork->tags = implode(', ', $artwork->tags ?? []); 

        return view('member.artworks.edit', compact('artwork', 'categories'));
    }

    public function update(Request $request, Artwork $artwork): RedirectResponse
    {
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