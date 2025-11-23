<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Miss;

Route::get('/candidate/{id}', function ($id) {
    $candidate = Miss::withCount('votes')->findOrFail($id);
    
    // Ajouter l'âge calculé
    $candidateData = $candidate->toArray();
    $candidateData['age'] = \Carbon\Carbon::parse($candidate->date_naissance)->age;
    
    return response()->json($candidateData);
});
