<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\Auth\MissAuthController;
use App\Http\Controllers\MissdashController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
Route::get('/candidates/{miss}', [CandidateController::class, 'show'])->name('candidates.show');

// Candidate Registration
Route::get('/register-candidate', [CandidateController::class, 'create'])->name('candidates.create');
Route::post('/register-candidate', [CandidateController::class, 'store'])->name('candidates.store');
Route::post('/logout', [CandidateController::class, 'logout'])->name('logout');

// Voting System
Route::get('/vote/{miss}', [VoteController::class, 'show'])->name('vote.show');
Route::post('/vote/{miss}/process', [VoteController::class, 'process'])->name('vote.process');
Route::get('/vote/{miss}/success', [VoteController::class, 'success'])->name('vote.success');


// Routes protégées candidates (middleware auth:miss)
Route::middleware('auth:miss')->group(function () {
    Route::get('/dashboard', [CandidateController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit', [CandidateController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CandidateController::class, 'update'])->name('profile.update');
});

// Routes protégées admins
Route::controller(AdminController::class)->group(function () {
    Route::get('/adminloginmaisjustedutextepourplusdesecurite', 'login')->name('connexion');
    Route::post('/adminloginmaisjustedutextepourplusdesecurite', 'checkLogin');
    
    // Route dashboard simple - accessible à l'utilisateur restreint
    Route::get('/dashBoardPourEviterLesAttaques', [\App\Http\Controllers\SimpleDashController::class, 'index'])->name('simpleDashboard')->middleware('auth:admin');
    Route::post('/admin/logout', 'logout')->name('admin.logout')->middleware('auth:admin');
    
    // Routes restreintes - interdites à l'utilisateur restreint
    Route::middleware(['auth:admin', 'restricted.admin'])->group(function () {
        Route::get('/dashboardAdmin', 'dashboard')->name('dashboardAdmin');
        Route::post('/admin/approve/{id}', 'approuve')->name('admin.approve');
        Route::post('/admin/approve-all', 'approveAll')->name('admin.approve.all');
        Route::delete('/admin/reject/{id}', 'refuse')->name('admin.reject');
        Route::post('/admin/restrict/{id}', 'restrict')->name('admin.restrict');
        Route::post('/admin/activate/{id}', 'activate')->name('admin.activate');
        // Anciennes routes pour compatibilité
        Route::get('/approuve/{id}', 'approuve')->name('approuve');
        Route::get('/refuse/{id}', 'refuse')->name('refuse');
    });
});

// Routes protégées candidates (Missdash)
Route::controller(MissdashController::class)->group(function () {
    Route::get('/connexion', 'login')->name('MissConnexion');
    Route::post('/connexion', 'checkLogin');
    
    // Routes protégées pour les candidates authentifiées
    Route::middleware('auth:miss')->group(function () {
        Route::get('/dashboardMiss', 'index')->name('dashboardMiss');
        Route::post('/addmedia', 'addmedia');
        Route::post('/updateinfo', 'updateinfo')->name('updateinfo');
        Route::post('/modifiermedia', 'modifiermedia');
    });
});

