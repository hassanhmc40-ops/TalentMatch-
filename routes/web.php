<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('offres', JobOfferController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->middleware('verified');

    Route::get('offres/{offre}/candidats/soumettre', [JobOfferController::class, 'createCandidate'])
        ->name('offres.candidats.create')
        ->middleware('verified');

    Route::post('offres/{offre}/candidats', [JobOfferController::class, 'submitCandidate'])
        ->name('offres.candidats.submit')
        ->middleware('verified');

    Route::get('offres/{offre}/analyses/{analyse}', [JobOfferController::class, 'showAnalysis'])
        ->name('analyses.show')
        ->middleware('verified');

    Route::get('conversations/{offre}/{candidat}', [ConversationController::class, 'show'])
        ->name('conversations.show')
        ->middleware('verified');

    Route::post('conversations/{offre}/{candidat}', [ConversationController::class, 'store'])
        ->name('conversations.store')
        ->middleware('verified');

    Route::get('conversations/{offre}/{candidat}/stream', [ConversationController::class, 'stream'])
        ->name('conversations.stream')
        ->middleware('verified');
});

require __DIR__.'/auth.php';
