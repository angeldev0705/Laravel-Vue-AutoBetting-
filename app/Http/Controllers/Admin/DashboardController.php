<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Filter;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $cacheTtl = 30*60; // 30 minutes

    public function getData(Request $request, string $key)
    {
        $method = 'get' . Str::studly($key);

        return method_exists($this, $method)
            ? $this->successResponse(['data' => call_user_func_array([$this, $method], [$request])])
            : $this->errorResponse(__('Unknown key'));
    }

    protected function getUsersCount(): int
    {
        return Cache::remember('admin.dashboard.users-count', $this->cacheTtl, function () {
            return User::all()->count();
        });
    }

    protected function getUsersBase(Request $request): Collection
    {
        // user sign ups grouped by difference in days between now and sign up date
        $signUps = Cache::remember('admin.dashboard.users-base', $this->cacheTtl, function () {
            return User::selectRaw('ABS(DATEDIFF(created_at, CURDATE())) AS diff, COUNT(*) AS signup_count')
                ->where('created_at', '>', Carbon::now()->subDays(6))
                ->groupBy('diff')
                ->orderBy('diff', 'asc')
                ->get()
                ->keyBy('diff')
                ->map(function ($item) {
                    return $item['signup_count'];
                });
        });

        $carry = $this->getUsersCount();

        // cumulative user base growth over last 7 days
        return collect()->pad(7, 0)
            ->map(function ($item, $key) use ($signUps, &$carry) {
                if ($key > 0) {
                    $carry = $carry - ($signUps[$key-1] ?? 0);
                }

                return  $carry;
            })
            ->reverse()
            ->values();
    }

    protected function getUsersSignUpsComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return User::where(function (Builder $query) use ($period) {
                    Filter::byPeriod($query, NULL, $period);
                })
                ->count();
        };

        return $this->getComparison('users-sign-ups', $getData);
    }

    protected function getUsersSignUpsByMethod(Request $request): Collection
    {
        $getData = function () {
            return User::selectRaw('IFNULL(social_profiles.provider_name, "email") AS source, COUNT(*) AS count')
                ->where(function (Builder $query) {
                    Filter::byPeriod($query, 'users');
                })
                ->leftJoin('social_profiles', 'social_profiles.user_id', '=', 'users.id')
                ->groupBy('source')
                ->orderBy('count', 'desc')
                ->get()
                ->map
                ->setAppends([]);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.users-sign-ups-by-method', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAffiliatesCommissionsHistory(Request $request): Collection
    {
        return Cache::remember('admin.dashboard.affiliates-comissions-history', $this->cacheTtl, function () {
            $commissions = AffiliateCommission::selectRaw('SUM(amount) AS commissions_total, WEEK(created_at, 1) AS week_number')
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->commissions_total;

            $commissionsByWeek = collect();

            for ($i = 7; $i >= 0; $i--) {
                $weekNumber = Carbon::now()->subWeeks($i)->weekOfYear;
                $commissionsByWeek->put($weekNumber, $commissions->has($weekNumber) ? $commissions->get($weekNumber) : 0);
            }

            return $commissionsByWeek->values();
        });
    }

    protected function getAffiliatesReferralsComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return User::where(function (Builder $query) use ($period) {
                    Filter::byPeriod($query, NULL, $period);
                })
                ->whereNotNull('referrer_id')
                ->count();
        };

        return $this->getComparison('affiliates-referrals', $getData);
    }

    protected function getAffiliatesCommissionsByStatus(Request $request): Collection
    {
        $getData = function () {
            return collect([
                (float) AffiliateCommission::where(function (Builder $query) {
                    Filter::byPeriod($query);
                })
                ->pending()
                ->sum('amount'),
                (float) AffiliateCommission::where(function (Builder $query) {
                    Filter::byPeriod($query);
                })
                ->approved()
                ->sum('amount'),
                (float) AffiliateCommission::where(function (Builder $query) {
                    Filter::byPeriod($query);
                })
                ->rejected()
                ->sum('amount')
            ]);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.affiliates-commissions-by-status', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAffiliatesCommissionsByType(Request $request): Collection
    {
        $getData = function () {
            return AffiliateCommission::select(
                    'type',
                    DB::raw('COUNT(id) AS commissions_count'),
                    DB::raw('SUM(amount) AS commissions_total')
                )
                ->where(function (Builder $query) {
                    Filter::byPeriod($query);
                })
                ->groupBy('type')
                ->orderBy('type')
                ->get()
                ->map
                ->setAppends(['title']);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.affiliates-commissions-by-type', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getGamesBetsComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return Game::completed()
                ->where(function (Builder $query) use ($period) {
                    Filter::byPeriod($query, NULL, $period);
                })
                ->count();
        };

        return $this->getComparison('games-bets', $getData);
    }

    protected function getGamesHouseEdgeComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return Game::completed()
                ->selectRaw('100 * (1 - SUM(win) / SUM(bet)) AS house_edge')
                ->where(function (Builder $query) use ($period) {
                    Filter::byPeriod($query, NULL, $period);
                })
                ->first()
                ->house_edge ?: 0;
        };

        return $this->getComparison('games-house-edge', $getData);
    }

    protected function getGamesWageredByWeek(Request $request): Collection
    {
        return Cache::remember('admin.dashboard.games-wagered-by-week', $this->cacheTtl, function () {
            $wagered = Game::completed()
                ->selectRaw('SUM(bet) AS bet_total, WEEK(created_at, 1) AS week_number')
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->bet_total;

            $wageredByWeek = collect();
            for ($i = 7; $i >= 0; $i--) {
                $weekNumber = Carbon::now()->subWeeks($i)->weekOfYear;
                $wageredByWeek->put($weekNumber, $wagered->has($weekNumber) ? $wagered->get($weekNumber) : 0);
            }

            return $wageredByWeek->values();
        });
    }

    protected function getGamesStats(Request $request): Collection
    {
        $getData = function () {
            return Game::completed()
                ->selectRaw('gameable_type,
                    SUM(bet) AS bet_total,
                    SUM(win) AS win_total,
                    100 * (1 - SUM(win) / SUM(bet)) AS house_edge')
                ->where(function (Builder $query) {
                    Filter::byPeriod($query);
                    Filter::byUserRoles($query);
                })
                ->groupBy('gameable_type')
                ->orderBy('gameable_type', 'asc')
                ->get()
                ->map
                ->makeHidden(['created']);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.games-stats', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getComparison(string $cacheKey, $getData): Collection
    {
        $currentPeriod = request()->query('period') ?: 'month';
        $previousPeriod = 'prev_' . $currentPeriod;

        $previousPeriodData = $currentPeriod == 'month'
            ? Cache::remember('admin.dashboard.' . $cacheKey . '-previous', $this->cacheTtl, function() use ($getData, $previousPeriod) { return $getData($previousPeriod); })
            : $getData($previousPeriod);

        $currentPeriodData = $currentPeriod == 'month'
            ? Cache::remember('admin.dashboard.' . $cacheKey . '-current', $this->cacheTtl, function() use ($getData, $currentPeriod) { return $getData($currentPeriod); })
            : $getData($currentPeriod);

        return collect([$previousPeriodData, $currentPeriodData]);
    }
}
