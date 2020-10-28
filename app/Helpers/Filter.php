<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Filter
{
    /**
     * Filter by period
     *
     * @param $query - should not be type hinted, because it can be Builder or JoinClause class
     * @param string|NULL $table
     * @param string|NULL $period
     */
    public static function byPeriod($query, string $table = NULL, string $period = NULL)
    {
        $column = $table ? $table . '.created_at' : 'created_at';
        $period = $period ?: request()->query('period');

        if ($period == 'week') {
            $query->where($column, '>=', Carbon::now()->startOfWeek());
        } elseif ($period == 'prev_week') {
            $query->where($column, '>=', Carbon::now()->subWeek()->startOfWeek())
                ->where($column, '<', Carbon::now()->startOfWeek());
        } elseif ($period == 'month') {
            $query->where($column, '>=', Carbon::now()->startOfMonth());
        } elseif ($period == 'prev_month') {
            $query->where($column, '>=', Carbon::now()->subMonth()->startOfMonth())
                ->where($column, '<', Carbon::now()->startOfMonth());
        } elseif ($period == 'year') {
            $query->where($column, '>=', Carbon::now()->startOfYear());
        } elseif ($period == 'prev_year') {
            $query->where($column, '>=', Carbon::now()->subYear()->startOfYear())
                ->where($column, '<', Carbon::now()->startOfYear());
        }
    }

    /**
     * Filter by user role
     *
     * @param $query
     */
    public static function byUserRoles($query)
    {
        if ($roles = request()->query('roles')) {
            $roles = collect($roles);

            $query->whereHas('account', function (Builder $query) use ($roles) {
                $query->whereHas('user', function (Builder $query) use ($roles) {
                    $query->whereIn('role', $roles
                        ->map(function ($role) {
                            return constant(User::class . '::ROLE_' . strtoupper($role));
                        })
                        // remove NULLs (caused by non existing roles)
                        ->filter()
                    );
                });
            });
        }
    }
}
