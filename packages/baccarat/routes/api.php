<?php

// game routes
Route::name('games.baccarat.')
    ->namespace('Packages\Baccarat\Http\Controllers')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent'])
    ->group(function () {
        // games
        Route::post('api/games/baccarat/play', 'GameController@play')->name('play');
    });
