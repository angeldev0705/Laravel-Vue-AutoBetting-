<?php

namespace App\Http\Controllers;

use App\Helpers\Queries\LeaderboardQuery;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $query = new LeaderboardQuery(new User(), $request);

        $items = $query->getBuilder()->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }
}
