<?php

namespace App\Helpers\Queries;

class UserQuery extends Query
{
    protected $searchableColumns = ['name', 'email'];
}
