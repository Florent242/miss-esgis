<?php

namespace App\Http\Controllers;

use App\Models\Miss;
use App\Models\Vote;

class HomeController extends Controller
{
    public function index()
    {
        // Total des votes = uniquement pour les candidates actives (non restreintes)
        $totalVotes = Vote::whereHas('miss', function($query) {
            $query->where('statut', 'active');
        })->count();

        // Toutes les candidates actives triées par nombre de votes
        $activeMisses = Miss::withCount('votes')
            ->where('statut', 'active') 
            ->orderByDesc('votes_count')
            ->get();

        // Calculer le pourcentage pour chaque candidate par rapport au total
        foreach ($activeMisses as $candidate) {
            $candidate->percentage = $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
        }

        // Top 3 pour le podium avec calcul des pourcentages
        $topMiss = $activeMisses->first();
        $secondMiss = $activeMisses->skip(1)->first();
        $thirdMiss = $activeMisses->skip(2)->first();
        
        // Calculer les pourcentages du TOP 3 (pour les barres de progression)
        $top3Total = $activeMisses->take(3)->sum('votes_count');
        $topPercentage = $top3Total > 0 ? round(($topMiss->votes_count ?? 0) / $top3Total * 100, 1) : 0;
        $secondPercentage = $top3Total > 0 ? round(($secondMiss->votes_count ?? 0) / $top3Total * 100, 1) : 0;
        $thirdPercentage = $top3Total > 0 ? round(($thirdMiss->votes_count ?? 0) / $top3Total * 100, 1) : 0;
        
        // Candidates restantes (après le top 3)
        $otherMisses = $activeMisses->skip(3);

        return view('home', compact(
            'totalVotes', 'topMiss', 'activeMisses', 'secondMiss', 'thirdMiss', 'otherMisses',
            'topPercentage', 'secondPercentage', 'thirdPercentage', 'top3Total'
        ));
    }
}
