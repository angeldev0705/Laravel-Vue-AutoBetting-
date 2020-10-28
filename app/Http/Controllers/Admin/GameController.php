<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PackageManager;
use App\Helpers\Queries\GameQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetGame;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = new GameQuery(new Game(), $request);

        $items = $query->getBuilder()
            ->with('account', 'account.user')
            ->get()
            ->map(function ($game) {
                $game->account->user->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin', 'is_bot', 'is_active');
                return $game;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(GetGame $request, Game $game)
    {
        return response()->json([
            'game' => $game->load('account', 'account.user'),
        ]);
    }

    public function package(GetGame $request, Game $game, PackageManager $packageManager)
    {
        return response()->json([
            'id' => $packageManager->getPackageIdByClass($game->gameable_type)
        ]);
    }

    public function details(GetGame $request, Game $game)
    {
        // make all gameable hidden attributes visible
        $game->gameable->makeVisible($game->gameable->getHidden());

        return response()->json([
            'game' => $game
        ]);
    }
}
