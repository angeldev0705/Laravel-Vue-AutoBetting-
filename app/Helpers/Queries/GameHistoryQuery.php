<?php

namespace App\Helpers\Queries;

use Illuminate\Database\Eloquent\Builder;

class GameHistoryQuery extends Query
{
    protected $sortableColumns = [
        'title'         => 'gameable_type',
        'bet'           => 'bet',
        'win'           => 'win',
        'profit'        => '(win - bet)',
        'created_at'    => 'created_at'
    ];

    protected function getBaseBuilder(): Builder
    {
        return parent::getBaseBuilder()->completed();
    }
}
