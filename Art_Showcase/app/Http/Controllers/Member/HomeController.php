<?php

namespace App\Http\Controllers\Member; // <--- PASTIKAN NAMESPACE INI BENAR

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Diperlukan untuk logic multi-role

class HomeController extends Controller
{
    /**
     * Menangani routing dashboard berdasarkan peran (role).
     */
    public function index()
    {
        // Panggil logic pemisahan dashboard yang sudah kita rancang
        if (Auth::check()) {
            $user = Auth::user();

            switch ($user->role) {
                case 'admin':
                    return view('admin.dashboard.main');
                
                case 'curator':
                    // Periksa approval sebelum memberikan akses penuh
                    if (!$user->is_approved) {
                        return view('curator.dashboard.pending');
                    }
                    return view('curator.dashboard.main');
                
                case 'member':
                default:
                    return view('member.dashboard');
            }
        }
        
        // Seharusnya tidak tercapai karena sudah ada middleware 'auth', tapi sebagai fallback
        return redirect()->route('login');
    }
}