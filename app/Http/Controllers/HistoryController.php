<?php

namespace App\Http\Controllers;

use App\Helpers\PackageManager;
use App\Helpers\Queries\GameHistoryQuery;
use App\Helpers\Queries\GameLossQuery;
use App\Helpers\Queries\GameWinQuery;
use App\Http\Requests\GetGame;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HistoryController extends Controller
{
    public function user(Request $request)
    {
        $account = $request->user()->account;

        $query = new GameHistoryQuery(new Game(), $request);

        $items = $query
            ->where(['account_id', '=', $account->id])
            ->getBuilder()
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function recent(Request $request)
    {
        $query = new GameHistoryQuery(new Game(), $request);

        $items = $query
            ->where(['created_at', '>', Carbon::now()->subDays(3)])
            ->getBuilder()
            // Load relations with specific columns (such as name) that can be safely shared with all users.
            ->with('account:id,user_id', 'account.user:id,name,avatar')
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function wins(Request $request)
    {
        $query = new GameWinQuery(new Game(), $request);

        $items = $query->getBuilder()
            // Load relations with specific columns (such as name) that can be safely shared with all users.
            ->with('account:id,user_id', 'account.user:id,name,avatar')
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function losses(Request $request)
    {
        $query = new GameLossQuery(new Game(), $request);

        $items = $query->getBuilder()
            // Load relations with specific columns (such as name) that can be safely shared with all users.
            ->with('account:id,user_id', 'account.user:id,name,avatar')
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(GetGame $request, Game $game)
    {
        return response()->json([
            'game' => $game->load('account:id,user_id', 'account.user:id,name')
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

    public function verify(GetGame $request, Game $game)
    {
        // make all gameable hidden attributes visible
        $game->gameable->makeVisible($game->gameable->getHidden())->append('secret_attribute', 'secret_attribute_hint');
        $game->provablyFairGame->makeVisible(['secret', 'server_seed'])->append('client_hash', 'shift_value');

        return response()->json([
            'game' => $game
        ]);
    }
}
