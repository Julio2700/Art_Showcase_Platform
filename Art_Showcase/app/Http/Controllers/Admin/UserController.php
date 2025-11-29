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
    // Menampilkan daftar semua pengguna
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

    // Menghapus pengguna
    public function destroy(User $user): RedirectResponse
    {
        // Tidak boleh menghapus diri sendiri (Admin yang sedang login)
        if (auth()->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        // Hapus pengguna. Relasi 'onDelete('cascade')' di migrasi akan menghapus karya, komentar, dsb.
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', "Pengguna '{$user->name}' berhasil dihapus.");
    }
    
    /**
     * Mengubah Peran (Role) Pengguna.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'curator', 'member'])],
        ]);
        
        $user->update(['role' => $validated['role']]);

        // Jika diubah menjadi non-curator, set is_approved menjadi true (untuk Member/Admin)
        if ($validated['role'] !== 'curator') {
             $user->update(['is_approved' => true]);
        }
        
        return back()->with('success', "Peran {$user->name} berhasil diubah menjadi {$validated['role']}.");
    }

    /**
     * Memperbarui Status Persetujuan (Approval) Curator.
     */
    public function updateApproval(User $user): RedirectResponse
    {
        if ($user->role !== 'curator') {
            return back()->with('error', 'Gagal: Fitur persetujuan hanya berlaku untuk peran Curator.');
        }

        $newStatus = !$user->is_approved;
        $user->update(['is_approved' => $newStatus]);
        
        $message = $newStatus ? 'disetujui' : 'ditangguhkan';
        return back()->with('success', "Status persetujuan Curator '{$user->name}' berhasil {$message}.");
    }
}