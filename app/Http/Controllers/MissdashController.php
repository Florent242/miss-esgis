<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminFilterRequest;
use App\Http\Requests\MediaFilterRequest;
use App\Http\Requests\UpdateInfoFilterRequest;
use App\Http\Requests\UpdateMediaFilterRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Media;
use App\Models\Miss;
use App\Models\Vote;
use App\Models\VoteLog;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MissdashController extends Controller
{
    public function login() : View {
        return view("candidates.login");
    }

    public function checkLogin(AdminFilterRequest $req) : RedirectResponse | View {
         $candidate=Miss::where('email', $req->validated('email'))->first();
         if($candidate)
         {
            // Vérifier le statut AVANT de vérifier le mot de passe
            if ($candidate->statut === 'pending') {
                return redirect()->route("MissConnexion")->with('error', "Votre candidature est en cours de validation. Vous recevrez un email dès qu'elle sera approuvée.");
            }
            
            if ($candidate->statut === 'reject') {
                return redirect()->route("MissConnexion")->with('error', "Votre candidature n'a pas été approuvée. Contactez l'administration pour plus d'informations.");
            }
            
            // Vérifier le mot de passe seulement si statut = active
            if ($candidate->statut === 'active' && Hash::check($req->validated('password'), $candidate->mot_de_passe))
            {
                Auth::guard('miss')->login($candidate);
                session()->regenerate();
                return redirect()->route("dashboardMiss");
            }
         }
        return redirect()->route("MissConnexion")->with('error', "Identifiant ou mot de passe incorrect");
    }

    public function index() : View | RedirectResponse {
        if(Auth::guard('miss')->check())
        {
            $stat= Miss::withCount(['photos','videos','votes'])->orderByDesc('votes_count')->get();
            $missId =Auth::guard('miss')->user()->id;
            $medias= Media::all()->where('miss_id',$missId);
            $candidate = $stat->firstWhere('id',$missId);
            $rang = $stat->search(fn($m)=> $m->id === $missId) +1 ;
            $totalcandidates = Miss::where('statut','active')->count();
            
            // Calculer les votes retirés (redirigés depuis cette candidate)
            $votesRetires = VoteLog::where('old_miss_id', $missId)->count();
            
            // Calculer le total des votes de toutes les candidates actives pour les pourcentages
            $totalVotesTousActives = Vote::whereHas('miss', function($query) {
                $query->where('statut', 'active');
            })->count();
            
            // Calculer le pourcentage de cette candidate
            $pourcentageCandidate = $totalVotesTousActives > 0 ? 
                round(($candidate->votes_count / $totalVotesTousActives) * 100, 1) : 0;
            
            // Calculer les pourcentages pour toutes les candidates (pour comparaison)
            foreach ($stat as $miss) {
                $miss->percentage = $totalVotesTousActives > 0 ? 
                    round(($miss->votes_count / $totalVotesTousActives) * 100, 1) : 0;
            }
            
            // Statistiques supplémentaires
            $votesRecus = Vote::where('miss_id', $missId)->count(); // Votes effectivement reçus
            $votesOriginaux = Vote::where('miss_id', $missId)->where('is_redirected', false)->count(); // Votes directs
            $votesRediriges = Vote::where('miss_id', $missId)->where('is_redirected', true)->count(); // Votes reçus par redirection
            
            // Afficher le nombre exact de votes
            $voteCount = $candidate->votes_count;
            $intervalleVotes = $voteCount . ' vote' . ($voteCount > 1 ? 's' : '');
            
            return view("candidates.dashboard", [
                'medias' => $medias,
                'candidate' => $candidate,
                'rang' => $rang,
                'totalcandidates' => $totalcandidates,
                'votesRetires' => $votesRetires,
                'pourcentageCandidate' => $pourcentageCandidate,
                'totalVotes' => $totalVotesTousActives,
                'votesRecus' => $votesRecus,
                'votesOriginaux' => $votesOriginaux,
                'votesRediriges' => $votesRediriges,
                'intervalleVotes' => $intervalleVotes,
                'allCandidates' => $stat // Pour afficher le classement avec pourcentages
            ]);
        }
         return redirect()->route("MissConnexion")->with('error', "Veuillez vous connecter");
    }

    public function addmedia(MediaFilterRequest $req) {
        $missId=Auth::guard('miss')->user()->id;
        $miss =Miss::find($missId);
        $countphoto =$miss->photos()->count();
        $countvideo=$miss->videos()->count();
        
        if(isset($req->validated()["photo"]) && $countphoto < 5)
        {
             $filename ="miss".Auth::guard('miss')->user()->id.time().'.'.$req->validated()["photo"]->extension();
             $photo = new Media();
             $photo->miss_id =$missId;
             $photo->type="photo";
             $photo->url=$filename;
             $req->validated()["photo"]->storeAs('media', $filename, 'public');
             $photo->save();
        }
        if(isset($req->validated()["video"]) && $countvideo < 1)
        {
             $videoname ="missvid".Auth::guard('miss')->user()->id.time().'.'.$req->validated()["video"]->extension();
             $video = new Media();
             $video->miss_id =$missId;
             $video->type="video";
             $video->url=$videoname;
             $req->validated()["video"]->storeAs('media', $videoname, 'public');
             $video->save();
        }
        return redirect()->back();
    }
    public function updateinfo(UpdateInfoFilterRequest $req)
    {
        try {
            $missId = Auth::guard('miss')->user()->id;
            $miss = Miss::find($missId);
            
            if (!$miss) {
                \Log::error('Miss not found for updateinfo', ['miss_id' => $missId]);
                return redirect()->back()->with('error', 'Candidate introuvable');
            }
            
            // Log des données reçues
            \Log::info('Update info attempt', [
                'miss_id' => $missId,
                'data' => $req->validated()
            ]);
            
            // Correction du mapping des champs (SANS ville - colonne inexistante)
            $miss->nom = $req->validated("nom");
            $miss->prenom = $req->validated("prenom");
            // VILLE IGNORÉE - colonne n'existe pas en production
            $miss->pays = $req->validated("pays");
            $miss->bio = $req->validated("bio");
            
            $miss->save();
            
            \Log::info('Update info success', ['miss_id' => $missId]);
            return redirect()->back()->with('success', 'Informations mises à jour avec succès');
            
        } catch (\Exception $e) {
            \Log::error('Update info failed', [
                'miss_id' => $missId ?? null,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function modifiermedia(UpdateMediaFilterRequest $req)
    {
        $missId=Auth::guard('miss')->user()->id;

        if($req->validated('idmiss'))
        {

            $miss= Miss::find($missId);
            $lastname=basename($miss->photo_principale);
            $filename ="missprofil".Auth::guard('miss')->user()->id.time().'.'.$req->validated()["photo"]->extension();
            $req->validated()["photo"]->storeAs('media', $filename, 'public');
            $miss->photo_principale = $filename;
            if(file_exists(storage_path('app/public/media/'.$lastname))) {
                unlink(storage_path('app/public/media/'.$lastname));
            }
            $miss->save();
            return redirect()->back();
        }
        $media=Media::find($req->validated('id'));

        if($media->miss_id == $missId){
        $lastname=basename($media->url);
         if(isset($req->validated()["photo"]))
        {
             $filename ="miss".Auth::guard('miss')->user()->id.time().'.'.$req->validated()["photo"]->extension();
             $req->validated()["photo"]->storeAs('media', $filename, 'public');
             $media->url=$filename;
             $media->save();
             if(file_exists(storage_path('app/public/media/'.$lastname))) {
                 unlink(storage_path('app/public/media/'.$lastname));
             }
        }
          if(isset($req->validated()["video"]))
        {
             $filename ="missvid".Auth::guard('miss')->user()->id.time().'.'.$req->validated()["video"]->extension();
             $req->validated()["video"]->storeAs('media', $filename, 'public');
             $media->url=$filename;
             $media->save();
             if(file_exists(storage_path('app/public/media/'.$lastname))) {
                 unlink(storage_path('app/public/media/'.$lastname));
             }
        }
        return redirect()->back();

    }
}
    /**
     * Calculer l'intervalle de votes pour l'affichage discret
     */
    private function getVoteInterval($voteCount)
    {
        if ($voteCount == 0) {
            return "0 vote";
        } elseif ($voteCount <= 10) {
            return "1-10 votes";
        } elseif ($voteCount <= 25) {
            return "11-25 votes";
        } elseif ($voteCount <= 50) {
            return "26-50 votes";
        } elseif ($voteCount <= 100) {
            return "51-100 votes";
        } elseif ($voteCount <= 200) {
            return "101-200 votes";
        } elseif ($voteCount <= 500) {
            return "201-500 votes";
        } elseif ($voteCount <= 1000) {
            return "501-1000 votes";
        } else {
            return "1000+ votes";
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
