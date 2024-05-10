<?php

use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/create-player', function () {
    return view('playerRegistration');
});

Route::post('/player', [PlayerController::class, 'create'])->name('create-player');

Route::get('/players', [PlayerController::class, 'listAll']);

Route::post('/generate-team', [PlayerController::class, 'generateTeam'])->name('generate-team');

// Route::post('/update-players-list', [PlayerController::class, 'updatePlayers']);

Route::get('/teams', [PlayerController::class, 'listTeams']);
