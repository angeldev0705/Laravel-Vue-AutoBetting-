<?php

namespace App\Providers;

use App\Events\GamePlayed;
use App\Listeners\AffiliateEventSubscriber;
use App\Listeners\CreateAccount;
use App\Listeners\SignUpBonus;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Coinbase\CoinbaseExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Yahoo\YahooExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SocialiteWasCalled::class => [
            YahooExtendSocialite::class,
            CoinbaseExtendSocialite::class
        ],
        Registered::class => [
            CreateAccount::class,
            SignUpBonus::class
        ],
        GamePlayed::class => [
            //
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        AffiliateEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // if email verification is enabled add dynamic event listener for the Registered event (to send an email verification link).
        if (config('settings.email_verification')) {
            Event::listen(Registered::class, SendEmailVerificationNotification::class);
        }
    }
}
