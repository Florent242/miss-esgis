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
            //'email' => 'nullable|email',           // si tu veux valider l’email
            //'numero_telephone' => 'nullable|string' // idem téléphone
        ]);

        Log::info('Nombre de votes reçu: ' . $validated['nombre_de_votes']);

        $miss = Miss::findOrFail($missId);

        if ($miss->statut !== 'active') {
            return response()->json(['error' => 'Cette candidate ne peut pas recevoir de votes pour le moment'], 400);
        }

        // Vérifie si transaction existe déjà avec la bonne colonne 'reference'
        $existingTransaction = Transaction::where('reference', $validated['transaction_id'])->first();
        if ($existingTransaction) {
            return response()->json(['error' => 'Transaction déjà enregistrée'], 409);
        }

        try {
            DB::beginTransaction();

            // Enregistrer la transaction
            $transaction = Transaction::create([
                'reference' => $validated['transaction_id'],
                'miss_id' => $miss->id,
                'montant' => $validated['montant'],
                'methode' => $validated['moyen_paiement'], // attention au champ méthode, dans ta table c’est 'methode' pas 'moyen_paiement'
                'statut' => 'completed',
                //'email' => $request->email ?? null,
                //'numero_telephone' => $request->numero_telephone ?? null,
            ]);

            // Enregistrer chaque vote individuellement
            for ($i = 0; $i < $validated['nombre_de_votes']; $i++) {
                Vote::create([
                    'miss_id' => $miss->id,
                    'transaction_id' => $transaction->id,
                    'moyen_paiement' => $validated['moyen_paiement'],
                    'montant' => 100, // prix unitaire vote
                    //'numero_telephone' => $request->numero_telephone ?? null,
                    //'email' => $request->email ?? null,
                ]);
            }

            DB::commit();

            // Retourne la référence externe et non un champ inexistant transaction_id dans ta table transaction
            return response()->json(['success' => true, 'transaction_reference' => $transaction->reference]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement du vote: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors du traitement de votre vote'], 500);
        }
    }

    public function success(Miss $miss)
    {
        return view('vote.success', compact('miss'));
    }
}
