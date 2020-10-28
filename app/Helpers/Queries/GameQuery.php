<?php

namespace App\Helpers\Queries;

use Illuminate\Database\Eloquent\Builder;

class GameQuery extends Query
{
    protected $sortableColumns = [
        'id'            => 'id',
        'title'         => 'gameable_type',
        'bet'           => 'bet',
        'win'           => 'win',
        'profit'        => '(win - bet)',
        'created_at'    => 'created_at'
    ];

    protected function getBaseBuilder(): Builder
    {
        return $this->getModel()::completed()
            ->when($this->getSearch(), function($query) {
                $query->whereHas('account.user', function ($query) {
                    $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->getSearch()) . '%']);
                    $query->orWhere('id', intval($this->getSearch()));
                });
            });
    }
}
