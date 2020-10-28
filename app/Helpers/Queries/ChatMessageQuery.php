<?php

namespace App\Helpers\Queries;

use Illuminate\Database\Eloquent\Builder;

class ChatMessageQuery extends Query
{
    protected function getBaseBuilder(): Builder
    {
        return $this->getModel()::when($this->getSearch(), function($query) {
            $query->whereHas('user', function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->getSearch()) . '%']);
                $query->orWhere('id', intval($this->getSearch()));
            });
        });
    }
}
