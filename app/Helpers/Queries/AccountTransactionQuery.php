<?php

namespace App\Helpers\Queries;

class AccountTransactionQuery extends Query
{
    protected $sortableColumns = ['id', 'amount', 'balance', 'created_at'];
}
