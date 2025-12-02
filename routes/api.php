<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Miss;
use App\Http\Controllers\SandboxPaymentController;
use App\Http\Controllers\SmsWebhookController;
use App\Http\Controllers\FedaPayController;

Route::get('/candidate/{id}', function ($id) {
    $candidate = Miss::withCount('votes')->findOrFail($id);
    
    // Ajouter l'âge calculé
    $candidateData = $candidate->toArray();
    $candidateData['age'] = \Carbon\Carbon::parse($candidate->date_naissance)->age;
    
    return response()->json($candidateData);
});

// Payment Routes (Production & Sandbox partagent le même contrôleur)
Route::prefix('payment')->group(function () {
    Route::post('/initiate', [SandboxPaymentController::class, 'initiate']);
    Route::post('/status', [SandboxPaymentController::class, 'checkStatus']);
    Route::get('/operators', [SandboxPaymentController::class, 'getOperators']);
});

// Sandbox Payment Routes (legacy - pour compatibilité)
Route::prefix('sandbox')->group(function () {
    Route::post('/initiate', [SandboxPaymentController::class, 'initiate']);
    Route::post('/status', [SandboxPaymentController::class, 'checkStatus']);
    Route::get('/operators', [SandboxPaymentController::class, 'getOperators']);
});

// SMS Gateway Webhook (sécurisé par API key)
Route::post('/webhook/sms', [SmsWebhookController::class, 'receive']);

// FedaPay Payment Routes
Route::prefix('fedapay')->group(function () {
    Route::post('/initiate', [FedaPayController::class, 'initiate']);
    Route::match(['get', 'post'], '/webhook', [FedaPayController::class, 'webhook'])->name('fedapay.webhook');
    Route::match(['get', 'post'], '/callback', [FedaPayController::class, 'callback'])->name('fedapay.callback');
    Route::post('/status', [FedaPayController::class, 'checkStatus']);
    Route::get('/test', [FedaPayController::class, 'testConnection']);
});

// FedaPay cURL API Routes (compatible avec aikovps)
Route::post('/transactions', [FedaPayController::class, 'createTransaction']);
