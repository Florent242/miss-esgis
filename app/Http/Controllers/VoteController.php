<?php

namespace App\Http\Controllers;

use App\Models\Miss;
use App\Models\Transaction;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    public function show(Miss $miss)
    {
        if ($miss->statut !== 'active') {
            abort(404);
        }
        return view('vote.show', compact('miss'));
    }

    public function process(Request $request, $missId)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:100',
            'moyen_paiement' => 'required|string',
            'transaction_id' => 'required|string',
            'nombre_de_votes' => 'required|integer|min:1',
            'numero_telephone' => 'nullable|string|max:20',
        ]);

        Log::info('Vote pour candidate: ' . $missId . ' - Nombre de votes: ' . $validated['nombre_de_votes']);

        $originalMiss = Miss::findOrFail($missId);

        if ($originalMiss->statut !== 'active') {
            return response()->json(['error' => 'Cette candidate ne peut pas recevoir de votes pour le moment'], 400);
        }

        $existingTransaction = Transaction::where('reference', $validated['transaction_id'])->first();
        if ($existingTransaction) {
            return response()->json(['error' => 'Transaction déjà enregistrée'], 409);
        }

        try {
            DB::beginTransaction();

            // Créer la transaction ORIGINE d'abord
            $transaction = Transaction::create([
                'reference' => $validated['transaction_id'],
                'miss_id' => $missId, // Candidate ORIGINALE dans la transaction
                'montant' => $validated['montant'],
                'numero_telephone' => $validated['numero_telephone'] ?? null,
                'methode' => $validated['moyen_paiement'],
                'statut' => 'completed',
            ]);

            // Créer le vote directement pour la candidate originale
            $vote = Vote::create([
                'transaction_id' => $transaction->id,
                'miss_id' => $missId,
                'nombre_de_votes' => $validated['nombre_de_votes'],
                'is_redirected' => false
            ]);

            Log::info('Vote créé pour candidate', [
                'miss_id' => $missId,
                'votes_count' => $validated['nombre_de_votes'],
                'transaction_id' => $transaction->id
            ]);

            DB::commit();

            return response()->json(['success' => true, 'transaction_reference' => $transaction->reference]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement du vote: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors du traitement de votre vote'], 500);
        }
    }

    public function success(Miss $miss)
    {
        // Vérifier qu'il y a une transaction récente complétée pour cette candidate
        $reference = session('vote_reference');
        
        if (!$reference) {
            // Pas de référence en session, rediriger vers la page de vote
            return redirect()->route('vote.show', $miss->id)
                ->with('error', 'Aucun paiement en cours détecté');
        }
        
        // Vérifier que la transaction existe et est complétée
        $transaction = Transaction::where('reference', $reference)
            ->where('miss_id', $miss->id)
            ->where('statut', 'completed')
            ->first();
            
        if (!$transaction) {
            // Transaction non trouvée ou pas encore complétée
            return redirect()->route('vote.show', $miss->id)
                ->with('warning', 'Votre paiement est en cours de traitement. Veuillez patienter.');
        }
        
        // Nettoyer la session
        session()->forget('vote_reference');
        
        // Redirection automatique vers l'accueil après vote réussi
        return redirect()->route('home')
            ->with('success', "Merci d'avoir voté pour {$miss->prenom} {$miss->nom} ! Votre soutien est précieux.")
            ->with('voted_candidate', $miss->prenom . ' ' . $miss->nom);
    }
}
