<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use StandardDateFormat;

    const ROLE_USER  = 1;
    const ROLE_ADMIN = 2;
    const ROLE_BOT   = 4;

    const STATUS_ACTIVE  = 0;
    const STATUS_BLOCKED = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'hide_profit'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'totp_secret', 'referrer_id', 'referrer'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'bet_count'         => 'integer',
        'bet_total'         => 'float',
        'profit_total'      => 'float',
        'profit_max'        => 'float',
        'hide_profit'       => 'boolean',
        'banned_from_chat'  => 'boolean'
    ];

    /**
     * Auto-cast the following columns to Carbon
     *
     * @var array
     */
    protected $dates = [
        'last_login_at', 'email_verified_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar_url', 'gravatar_url', 'created_ago', 'role_title', 'status_title'
    ];

    /**
     * User, who referred current user (referrer)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referrer()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Users, referred by current user (referees)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referees()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query): Builder
    {
        return $query->where('users.status', '=', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include bots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBots($query): Builder
    {
        return $query->where('users.role', '=', self::ROLE_BOT);
    }

    /**
     * Scope a query to only include regular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegular($query)
    {
        return $query->where('users.role', '=', self::ROLE_USER);
    }

    /**
     * User account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account()
    {
        return $this->hasOne(Account::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(AccountTransaction::class, Account::class);
    }

    public function commission()
    {
        return $this->morphMany(AffiliateCommission::class, 'commissionable');
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, Account::class);
    }

    /**
     * Chat messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Custom garavatar_url attribute
     *
     * @return string
     */
    public function getGravatarUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=100&d=mp';
    }

    /**
     * Custom avatar_url attribute
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/avatars/' . $this->avatar) : $this->gravatar_url;
    }

    /**
     * Custom two_factor_auth_enabled attribute
     * @return bool
     */
    public function getTwoFactorAuthEnabledAttribute()
    {
        return $this->totp_secret ? TRUE : FALSE;
    }

    /**
     * Custom two_factor_auth_passed attribute
     * @return bool
     */
    public function getTwoFactorAuthPassedAttribute()
    {
        return request()->session()->get('two_factor_auth_passed', FALSE);
    }

    /**
     * Custom is_admin attribute
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Custom is_bot attribute
     * @return bool
     */
    public function getIsBotAttribute()
    {
        return $this->hasRole(self::ROLE_BOT);
    }

    /**
     * Custom is_active attribute
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Social profiles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany(SocialProfile::class);
    }

    /**
     * Override original model's delete() method
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->avatar)
            Storage::disk('public')->delete('avatars/' . $this->avatar);

        return parent::delete();
    }

    /**
     * Determine if the user has verified their email address.
     * Override Illuminate\Auth\MustVerifyEmail@hasVerifiedEmail()
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at) || $this->is_bot;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Check if user has given role
     *
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        return isset($this->role) && $this->role == $role;
    }

    // automatically decrypt TOTP secret when it's requested
    public function getTotpSecretAttribute($value)
    {
        return $value ? decrypt($value) : NULL;
    }

    // encrypt TOTP secret in the database
    public function setTotpSecretAttribute($value)
    {
        $this->attributes['totp_secret'] = encrypt($value);
    }

    public static function roles()
    {
        return [
            self::ROLE_USER => __('User'),
            self::ROLE_ADMIN => __('Admin'),
            self::ROLE_BOT => __('Bot'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_BLOCKED => __('Blocked'),
        ];
    }

    /**
     * Custom getter for created_ago attribute
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : NULL;
    }

    public function getRoleTitleAttribute()
    {
        return self::roles()[$this->role] ?? '';
    }

    public function getStatusTitleAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }
}
