<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use StandardDateFormat;

    const TYPE_SIGN_UP = 1; // regular user sign up bonus
    const TYPE_DEPOSIT = 2; // deposit bonus

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'title'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->morphOne(AccountTransaction::class, 'transactionable');
    }

    public function getTitleAttribute()
    {
        switch ($this->type) {
            case self::TYPE_SIGN_UP:
                return __('Sign up bonus');
                break;

            case self::TYPE_DEPOSIT:
                return __('Deposit bonus');
                break;

            default:
                return __('Bonus');
        }
    }
}
