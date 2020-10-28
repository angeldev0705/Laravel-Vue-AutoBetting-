<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model implements PercentageAffiliateCommission
{
    use StandardDateFormat;

    const STATUS_IN_PROGRESS = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_CANCELLED = 2;

    /**
     * The attributes that should be hidden from JSON output.
     *
     * @var array
     */
    protected $hidden = [
        'account_id',
        'gameable_id',
        'provably_fair_game_id',
        'gameable_type',
        'status'
    ];

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'bet'           => 'float',
        'bet_count'     => 'integer',
        'bet_total'     => 'float',
        'win'           => 'float',
        'win_count'     => 'integer',
        'win_total'     => 'float',
        'profit'        => 'float',
        'profit_total'  => 'float',
        'profit_max'    => 'float',
        'house_edge'    => 'float'
    ];

    protected $appends = ['is_completed', 'title', 'profit', 'created'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope a query to only include completed games.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query): Builder
    {
        return $query->where('games.status', '=', self::STATUS_COMPLETED);
    }

    public function provablyFairGame()
    {
        return $this->belongsTo(ProvablyFairGame::class);
    }

    public function gameable()
    {
        return $this->morphTo();
    }

    public function transaction()
    {
        return $this->morphOne(AccountTransaction::class, 'transactionable');
    }

    public function commission()
    {
        return $this->morphMany(AffiliateCommission::class, 'commissionable');
    }

    /**
     * Override original model's delete() method to delete (cascade) polymorphic relationships
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $this->transaction()->delete(); // delete linked transaction
        $this->gameable()->delete(); // delete specific game, e.g. Dice, Roulette
        return parent::delete();
    }

    /**
     * Getter for title attribute
     *
     * @return string
     */
    public function getTitleAttribute(): string
    {
        // add whitespaces to games titles, e.g. EuropeanRoulette => European Roulette
        return __( preg_replace('/([a-z])([A-Z0-9])/s','$1 $2', class_basename($this->gameable_type)));
    }

    /**
     * Getter for profit attribute
     *
     * @return float
     */
    public function getProfitAttribute(): float
    {
        return $this->win - $this->bet;
    }

    /**
     * Getter for is_in_progress attribute
     *
     * @return bool
     */
    public function getIsInProgressAttribute(): bool
    {
        return $this->status == self::STATUS_IN_PROGRESS;
    }

    /**
     * Getter for is_completed attribute
     *
     * @return bool
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * Getter for is_cancelled attribute
     *
     * @return bool
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    /**
     * Getter for created attribute
     *
     * @return int|null
     */
    public function getCreatedAttribute(): ?int
    {
        return $this->created_at ? $this->created_at->timestamp : NULL;
    }

    /**
     * Setter for is_completed attribute
     *
     * @param $secret
     */
    public function setIsCompletedAttribute(bool $isCompleted)
    {
        if ($isCompleted) {
            $this->attributes['status'] = self::STATUS_COMPLETED;
        }
    }

    /**
     * Setter for is_cancelled attribute, also implicitly sets seed and hash
     *
     * @param $secret
     */
    public function setIsCancelledAttribute(bool $isCancelled)
    {
        if ($isCancelled) {
            $this->attributes['status'] = self::STATUS_CANCELLED;
        }
    }

    public function getAffiliateCommissionBaseAmount(): float
    {
        return $this->bet;
    }
}
