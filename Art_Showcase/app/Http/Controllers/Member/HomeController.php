<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index(Request $request): View|RedirectResponse 
    {
        $user = Auth::user(); 

        if (!$user) {
             return redirect()->route('login');
        }

        switch ($user->role) {
            case 'admin':
                // Sekarang mengembalikan RedirectResponse diizinkan
                return redirect()->route('admin.dashboard');
                
            case 'curator':
                if (!$user->is_approved) {
                    return view('curator.dashboard.pending'); 
                }
                return view('curator.dashboard.main');
                
            case 'member':
            default:
                return view('member.dashboard');
        }
    }
}