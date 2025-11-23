<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminFilterRequest;
use App\Http\Requests\MediaFilterRequest;
use App\Http\Requests\UpdateInfoFilterRequest;
use App\Http\Requests\UpdateMediaFilterRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Media;
use App\Models\Miss;

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
            //dd($totalcandidates);
            return view("candidates.dashboard",['medias'=>$medias,'candidate'=>$candidate,'rang'=>$rang,'totalcandidates'=>$totalcandidates]);
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
        $missId=Auth::guard('miss')->user()->id;
        $miss=Miss::find($missId);
        $miss->nom= $req->validated("nom") ;
        $miss->prenom= $req->validated("prenom") ;
        $miss->pays= $req->validated("ville") ;
        $miss->bio= $req->validated("bio") ;
        $miss->save();
        return redirect()->back();
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
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
}
