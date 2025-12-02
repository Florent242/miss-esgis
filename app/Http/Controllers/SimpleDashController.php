<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Miss;
use App\Models\Vote;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class SimpleDashController extends Controller
{
    /**
     * Dashboard simplifié pour admin spécial
     */
    public function index()
    {
        // Vérifier que c'est le bon compte admin
        $adminEmail = Auth::guard('admin')->user()->email;
        if ($adminEmail !== 'r.31N3-35Gis@admin.com') {
            abort(403, 'Accès non autorisé');
        }

        // Statistiques de base
        $totalVotes = Vote::whereHas('miss', function($query) {
            $query->where('statut', 'active');
        })->count();

        // Candidates avec pourcentages
        $candidates = Miss::withCount('votes')
            ->orderByDesc('votes_count')
            ->get();

        // Calculer les pourcentages
        foreach ($candidates as $candidate) {
            $candidate->percentage = $totalVotes > 0 ? 
                round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
        }

        // Classement par statut
        $acceptees = Miss::where('statut', 'active')
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->get();

        $rejetees = Miss::where('statut', 'reject')
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->get();

        $restreintes = Miss::where('statut', 'restricted')
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->get();

        // Ajouter les pourcentages à chaque groupe
        foreach ([$acceptees, $rejetees, $restreintes] as $group) {
            foreach ($group as $candidate) {
                $candidate->percentage = $totalVotes > 0 ? 
                    round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
            }
        }

        return view('admin.simple-dashboard', compact(
            'totalVotes',
            'candidates',
            'acceptees', 
            'rejetees',
            'restreintes'
        ));
    }
}
