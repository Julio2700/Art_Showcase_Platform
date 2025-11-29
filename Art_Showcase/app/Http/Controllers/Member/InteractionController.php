<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Like;
use App\Models\Favorite;
use App\Models\Report;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    /**
     * Like/Unlike Karya.
     */
    public function toggleLike(Artwork $artwork): RedirectResponse
    {
        $like = Like::where('user_id', Auth::id())
                    ->where('artwork_id', $artwork->id)
                    ->first();

        if ($like) {
            $like->delete();
            $message = 'Like dibatalkan.';
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'artwork_id' => $artwork->id,
            ]);
            $message = 'Karya disukai!';
        }

        return back()->with('success', $message);
    }

    /**
     * Tambah/Hapus dari Favorite.
     */
    public function toggleFavorite(Artwork $artwork): RedirectResponse
    {
        $favorite = Favorite::where('user_id', Auth::id())
                            ->where('artwork_id', $artwork->id)
                            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Dihapus dari daftar favorit.';
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'artwork_id' => $artwork->id,
            ]);
            $message = 'Ditambahkan ke daftar favorit.';
        }

        return back()->with('success', $message);
    }

    /**
     * Tambah Komentar.
     */
    public function addComment(Request $request, Artwork $artwork): RedirectResponse
    {
        $validated = $request->validate(['content' => 'required|string|max:1000']);

        Comment::create([
            'user_id' => Auth::id(),
            'artwork_id' => $artwork->id,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Report Content.
     */
    public function reportContent(Request $request, Artwork $artwork): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500', // Alasan laporan
        ]);

        // Mencegah laporan ganda dari user yang sama
        $existingReport = Report::where('user_id', Auth::id())
                                ->where('artwork_id', $artwork->id)
                                ->where('status', 'pending')
                                ->first();
        
        if ($existingReport) {
            return back()->with('error', 'Anda sudah melaporkan karya ini. Menunggu tinjauan Admin.');
        }

        Report::create([
            'user_id' => Auth::id(),
            'artwork_id' => $artwork->id,
            'reason' => $validated['reason'],
            'status' => 'pending', // Default status saat laporan dibuat
        ]);

        return back()->with('success', 'Laporan Anda telah dikirim ke Moderation Queue.');
    }
}