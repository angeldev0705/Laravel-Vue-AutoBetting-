<?php

namespace App\Helpers\Queries;

use Illuminate\Database\Eloquent\Builder;

class AffiliateCommissionQuery extends Query
{
    protected function getBaseBuilder(): Builder
    {
        return $this->getModel()::when($this->getSearch(), function($query) {
            $query->whereHas('account.user', function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->getSearch()) . '%']);
                $query->orWhere('id', intval($this->getSearch()));
            });
        });
    }
}
