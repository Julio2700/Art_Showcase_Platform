<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CuratorApproval
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Jika bukan Curator, biarkan middleware lain yang handle
        if ($user->role !== 'curator') {
            return $next($request);
        }
        
        // 2. Jika Curator, periksa status persetujuan
        if ($user->role === 'curator' && $user->is_approved) {
            return $next($request); 
        }

        // 3. Jika Curator dan BELUM disetujui, redirect ke halaman pending
        if ($user->role === 'curator' && !$user->is_approved) {
            // Anda perlu route 'curator.pending' yang mengarah ke view pending.blade.php
            return redirect()->route('curator.pending'); 
        }

        abort(403);
    }
}