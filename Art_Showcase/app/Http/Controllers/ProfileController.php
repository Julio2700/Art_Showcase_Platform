<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; 

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(), 
        ]);
    }

    /**
     * Update the user's profile information (Termasuk Avatar dan Info Publik).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // 💡 Handle Avatar Upload (Poin Kritis)
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada (jika user->avatar_path adalah kolom yang benar)
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            // Simpan file baru
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = $path;
            
            // Hapus 'avatar' dari array validated agar tidak bentrok dengan fill()
            unset($validated['avatar']); 
        }
        
        // 💡 Handle Info Publik (display_name, bio)
        // Note: Asumsi ProfileUpdateRequest mengizinkan field ini
        if ($request->has('display_name')) {
             $validated['display_name'] = $request->input('display_name');
        }
        if ($request->has('bio')) {
             $validated['bio'] = $request->input('bio');
        }

        // Fill data yang tersisa (name, email, display_name, bio, avatar_path)
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // 💡 Tambahkan: Hapus avatar fisik saat akun dihapus
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}