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
