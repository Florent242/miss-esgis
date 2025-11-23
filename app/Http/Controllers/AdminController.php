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
            
            // Calculer l'âge pour chaque candidate
            foreach ($candidates as $candidate) {
                $candidate->age = \Carbon\Carbon::parse($candidate->date_naissance)->age;
            }
            
            return view('admin.dashboard',["candidates"=>$candidates,"transactions"=>$transactions,"candidatesaprouver"=>$candidatesaprouver]);
        }
         return redirect()->route("connexion")->with('error', "Veuillez vous connecter");
        
    }
    public function checkLogin(AdminFilterRequest $req) : RedirectResponse | View {
         $admin=Admin::where('email', $req->validated('email'))->first();
         if($admin)
         {
            // Vérifier si le mot de passe est hashé ou non
            $passwordMatch = false;
            if (password_verify($req->validated('password'), $admin->mot_de_passe)) {
                // Mot de passe hashé (bcrypt)
                $passwordMatch = true;
            } elseif ($admin->mot_de_passe === $req->validated('password')) {
                // Ancien format non hashé (à supprimer en production)
                $passwordMatch = true;
            }
            
            if ($passwordMatch)
            {
                Auth::guard('admin')->login($admin);
                session()->regenerate();
                return redirect()->route("dashboardAdmin");
            }
         }
        return redirect()->route("connexion")->with('error', "Identifiant incorrect");
    }

    public function logout(Request $request) : RedirectResponse {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route("connexion")->with('success', "Déconnexion réussie");
    }

    public function approuve($req)
    {
        $candidate = Miss::findOrFail($req);
        $candidate->statut = "active";
        $candidate->save();
        
        // Envoi email avec gestion d'erreur
        try {
            Mail::to($candidate->email)->send(new CandidatureApprouvee($candidate));
            return redirect()->route("dashboardAdmin")->with('success', 'Candidature acceptée et email envoyé à ' . $candidate->prenom . ' ' . $candidate->nom);
        } catch (\Throwable $e) {
            logger()->error('Erreur envoi mail approbation: ' . $e->getMessage());
            return redirect()->route("dashboardAdmin")->with('success', 'Candidature acceptée mais erreur lors de l\'envoi de l\'email');
        }
    }

    public function approveAll()
    {
        $pendingCandidates = Miss::where('statut', 'pending')->get();
        $count = $pendingCandidates->count();
        
        if ($count === 0) {
            return redirect()->route("dashboardAdmin")->with('info', 'Aucune candidate en attente.');
        }
        
        foreach ($pendingCandidates as $candidate) {
            $candidate->statut = "active";
            $candidate->save();
            
            // Envoi email avec gestion d'erreur
            try {
                Mail::to($candidate->email)->send(new CandidatureApprouvee($candidate));
            } catch (\Throwable $e) {
                logger()->error('Erreur envoi mail approbation pour ' . $candidate->email . ': ' . $e->getMessage());
            }
        }
        
        return redirect()->route("dashboardAdmin")->with('success', $count . ' candidate(s) approuvée(s) avec succès !');
    }

    public function refuse($req)
    {
        $candidate = Miss::findOrFail($req);
        $candidate->statut = "reject";
        $candidate->save();
        
        // Envoi email avec gestion d'erreur
        try {
            Mail::to($candidate->email)->send(new CandidatureRejetee($candidate));
            return redirect()->route("dashboardAdmin")->with('success', 'Candidature rejetée et email envoyé à ' . $candidate->prenom . ' ' . $candidate->nom);
        } catch (\Throwable $e) {
            logger()->error('Erreur envoi mail rejet: ' . $e->getMessage());
            return redirect()->route("dashboardAdmin")->with('success', 'Candidature rejetée mais erreur lors de l\'envoi de l\'email');
        }
    }

    public function restrict($id)
    {
        $candidate = Miss::findOrFail($id);
        $candidate->statut = "restricted";
        $candidate->save();
        
        // Envoi email de notification
        try {
            Mail::to($candidate->email)->send(new \App\Mail\CompteRestreint($candidate));
        } catch (\Throwable $e) {
            logger()->error('Erreur envoi mail restriction: ' . $e->getMessage());
        }
        
        return redirect()->route("dashboardAdmin")->with('success', 'Accès restreint pour ' . $candidate->prenom . ' ' . $candidate->nom);
    }

    public function activate($id)
    {
        $candidate = Miss::findOrFail($id);
        $candidate->statut = "active";
        $candidate->save();
        
        // Envoi email de notification de réactivation
        try {
            Mail::to($candidate->email)->send(new \App\Mail\CompteReactive($candidate));
            return redirect()->route("dashboardAdmin")->with('success', 'Accès activé et email envoyé à ' . $candidate->prenom . ' ' . $candidate->nom);
        } catch (\Throwable $e) {
            logger()->error('Erreur envoi mail réactivation: ' . $e->getMessage());
            return redirect()->route("dashboardAdmin")->with('success', 'Accès activé mais erreur lors de l\'envoi de l\'email');
        }
    }
}
