<?php

namespace App\Http\Controllers;

use App\Models\Miss;
use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CandidateSubmitted;

class CandidateController extends Controller
{
    /**
     * Liste toutes les candidates actives.
     */
    public function index()
    {
        $candidates = Miss::withCount('votes')
            ->where('statut', 'active')
            ->orderByDesc('votes_count')
            ->orderBy('prenom')
            ->get();

        return view('candidates.index', compact('candidates'));
    }

    /**
     * Affiche les détails d'une candidate.
     */
    public function show(Miss $miss)
    {
        if ($miss->statut !== 'active') {
            abort(404);
        }

        $photos = $miss->medias()->where('type', 'photo')->get();
        $video  = $miss->medias()->where('type', 'video')->first();

        // Calculer le total des votes de toutes les candidates actives pour le pourcentage
        $totalVotesTousActives = Vote::whereHas('miss', function($query) {
            $query->where('statut', 'active');
        })->count();
        
        // Calculer le pourcentage de cette candidate
        $pourcentageCandidate = $totalVotesTousActives > 0 ? 
            round(($miss->total_votes / $totalVotesTousActives) * 100, 1) : 0;
        
        // Ajouter le nombre exact de votes
        $nombreVotes = $miss->total_votes;

        return view('candidates.show', compact('miss', 'photos', 'video', 'pourcentageCandidate', 'nombreVotes'));
    }

    /**
     * Formulaire d'inscription candidate.
     */
    public function create()
    {
        return view('auth.register-candidate');
    }

    /**
     * Enregistre une nouvelle candidate.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'   => 'required|string|max:100',
            'prenom'=> 'required|string|max:100',
            'age'   => 'required|integer|min:16|max:30',
            'ville'=> 'required|string|max:100',
            'pays'  => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:misses,email',
            'password' => 'required|string|min:8|confirmed',
            'photo_principale' => 'required|image|max:5120',
            'bio'   => 'nullable|string',
        ]);

        // Stockage de la photo
        $filename = 'miss' . time() . '.' . $request->file('photo_principale')->extension();
        $request->file('photo_principale')->storeAs('media', $filename, 'public');

        // Calculer la date de naissance à partir de l'âge
        $dateNaissance = now()->subYears($request->age)->format('Y-m-d');

        $miss = Miss::create([
            'nom'              => $request->nom,
            'prenom'           => $request->prenom,
            'age'              => $request->age,
            'date_naissance'   => $dateNaissance,
            'pays'             => $request->ville.', '.$request->pays,
            'telephone'        => $request->telephone,
            'email'            => $request->email,
            'mot_de_passe'     => Hash::make($request->password),
            'photo_principale' => $filename,
            'bio'              => $request->bio,
            'statut'           => 'pending',
        ]);

        // Envoi d'un email de confirmation de réception de la candidature
        try {
            Mail::to($miss->email)->send(new CandidateSubmitted($miss));
        } catch (\Throwable $e) {
            // Vous pouvez logger l'erreur si nécessaire
            // logger()->error('Erreur envoi mail candidature: '.$e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Votre demande a été soumise et est en cours de validation. Veuillez vérifier votre boîte mail pour la suite.');
    }
}
