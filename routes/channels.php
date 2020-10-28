<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// All presence channels are also private channels; therefore,
// users must be authorized to access them.
// However, when defining authorization callbacks for presence channels,
// you will not return true if the user is authorized to join the channel.
// Instead, you should return an array of data about the user.
use App\Models\GameRoomPlayer;

Broadcast::channel('chat.{id}', function ($user, $chatRoomId) {
    return $user
        ? $user->only('id', 'name', 'avatar')
        : FALSE;
});

Broadcast::channel('game.{id}', function ($user, $gameRoomId) {
    return GameRoomPlayer::where('game_room_id', $gameRoomId)->where('user_id', $user->id)->count() > 0
        ? $user->only('id', 'name', 'avatar')
        : FALSE;
});
