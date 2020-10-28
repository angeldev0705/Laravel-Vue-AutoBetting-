<?php

namespace App\Http\Controllers\User;

use App\Helpers\Queries\AccountTransactionQuery;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function transactions(Request $request)
    {
        $account = $request->user()->account;

        $query = new AccountTransactionQuery(new AccountTransaction(), $request);

        $items = $query->where(['account_id', '=', $account->id])
            ->getBuilder()
            ->with('transactionable')
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }
}
