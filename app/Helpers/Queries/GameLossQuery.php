<?php

namespace App\Helpers\Queries;

use Illuminate\Database\Eloquent\Builder;

class GameLossQuery extends Query
{
    public function getOrderBy(): string
    {
        return 'bet';
    }

    public function getOrderDirection(): string
    {
        return 'desc';
    }

    public function getRowsToSkip(): int
    {
        return 0;
    }

    public function getItemsPerPage(): int
    {
        return 10;
    }

    public function calculateRowsCount(): int
    {
        return 10;
    }

    protected function getBaseBuilder(): Builder
    {
        return parent::getBaseBuilder()
            ->completed()
            ->where('win', 0);
    }
}
