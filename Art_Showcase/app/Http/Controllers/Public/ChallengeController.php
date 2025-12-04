<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function show(Challenge $challenge): View
    {
        // Load submissions yang diterima
        $submissions = $challenge->submissions()
                                 ->with(['artwork.user']) 
                                 ->paginate(20);
                                 
        // Cek apakah challenge sudah berakhir
        $is_over = $challenge->ends_at->isPast();
        
        $winners = $challenge->submissions()
                             ->where('is_winner', true)
                             ->with('artwork.user') 
                             ->orderBy('placement')
                             ->get();

        return view('public.challenge.show', compact('challenge', 'submissions', 'is_over', 'winners'));
    }
}