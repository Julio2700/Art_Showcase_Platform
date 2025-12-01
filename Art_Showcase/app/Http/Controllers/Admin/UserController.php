<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // READ: Menampilkan daftar semua pengguna
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all'); // Filter berdasarkan status

        $users = User::query()
            ->when($status === 'pending', function ($query) {
            return $query->where('role', 'curator')->where('is_approved', false);
            })
            ->when($status === 'curator', function ($query) {
                return $query->where('role', 'curator');
            })
            ->when($status === 'member', function ($query) {
                return $query->where('role', 'member');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'status'));
    }

    /**
     * UPDATE: Menampilkan formulir edit.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * UPDATE: Memperbarui data pengguna dari form admin/users/edit.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'curator', 'member'])],
            'is_approved' => 'nullable|boolean',
        ]);

        if (auth()->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat mengubah peran atau status persetujuan akun Anda sendiri.');
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Handle Logic Approval/Role Change
        if ($validated['role'] === 'curator') {
            // Set is_approved berdasarkan checkbox di form
            $updateData['is_approved'] = $request->has('is_approved');
        } else {
            // Jika diubah ke Member atau Admin, set is_approved menjadi true secara default
            $updateData['is_approved'] = true;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index', ['status' => $validated['role']]) 
                         ->with('success', "User '{$user->name}' berhasil diperbarui.");
    }
    
    /**
     * Memperbarui Status Persetujuan (Approval) Curator dari list INDEX (Tombol Terima).
     */
    public function updateApproval(User $user): RedirectResponse
    {
        if ($user->role !== 'curator') {
            return back()->with('error', 'Gagal: Fitur persetujuan hanya berlaku untuk peran Curator.');
        }

        // Set status menjadi TRUE
        $user->update(['is_approved' => true]);
        
        // Tambahkan fresh() untuk memastikan model memiliki data terbaru saat redirect
        $user->fresh(); 

        $message = 'disetujui';

        // 💡 PERBAIKAN: Arahkan ke filter 'curator'.
        return redirect()->route('admin.users.index', ['status' => 'curator']) 
                         ->with('success', "Curator '{$user->name}' berhasil {$message}.");
    }

    /**
     * Menghapus pengguna (Dipakai oleh Tombol Tolak/Hapus).
     */
    public function destroy(User $user): RedirectResponse
    {
        // Tidak boleh menghapus diri sendiri (Admin yang sedang login)
        if (auth()->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        $user->delete();

        return redirect()->route('admin.users.index', ['status' => 'all']) 
                         ->with('success', "Pengguna '{$user->name}' berhasil dihapus.");
    }
}