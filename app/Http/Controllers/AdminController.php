<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminFilterRequest;
use App\Models\Admin;
use App\Models\Miss;
use App\Models\Transaction;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Mail\CandidatureApprouvee;
use App\Mail\CandidatureRejetee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    //
    public function login() : View {
        return view("admin.login");
    }
    public function dashboard() : RedirectResponse | View
    {
        
        if(Auth::guard('admin')->check())
        {
            $transactions = Transaction::with('miss')->orderBy('date','desc')->get();
            $candidates= Miss::withCount('votes')->WhereYear('date_inscription',date('Y'))->orderBy('votes_count','desc')->get();
            $candidatesaprouver= Miss::withCount('votes')->where('statut','active')->WhereYear('date_inscription',date('Y'))->orderBy('votes_count','desc')->get();
            return view('admin.dashboard',["candidates"=>$candidates,"transactions"=>$transactions,"candidatesaprouver"=>$candidatesaprouver]);
        }
         return redirect()->route("connexion")->with('error', "Veuillez vous connecter");
        
    }
    public function checkLogin(AdminFilterRequest $req) : RedirectResponse | View {
         $admin=Admin::where('email', $req->validated('email'))->first();
         if($admin)
         {
            if ($admin->mot_de_passe === $req->validated('password') )
            {
                Auth::guard('admin')->login($admin);
                //session()->regenerate();
                return redirect()->route("dashboardAdmin");
            }
         }
        return redirect()->route("connexion")->with('error', "Identifiant incorrect");
    }

    public function approuve( $req)
    {
        $candidate = Miss::findOrFail($req);
        $candidate->statut="active";
        $candidate->save();
        Mail::to($candidate->email)->send(new CandidatureApprouvee($candidate));
        return redirect()->route("dashboardAdmin")->with('success','Candidature acceptée');
    }

      public function refuse( $req)
    {
        $candidate = Miss::findOrFail($req);
        $candidate->statut="reject";
        $candidate->save();
        Mail::to($candidate->email)->send(new CandidatureRejetee($candidate));
        return redirect()->route("dashboardAdmin")->with('success','Candidature rejetée');
    }
}
