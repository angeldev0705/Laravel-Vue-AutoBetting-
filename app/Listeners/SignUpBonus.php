<?php

namespace App\Listeners;

use App\Models\Bonus;
use App\Services\AccountService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignUpBonus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $user = $event->user;

        $accountService = new AccountService($user->account);

        // create sign up bonus
        $accountService->createBonus(
            Bonus::TYPE_SIGN_UP,
            config('settings.bonuses.sign_up')
        );
    }
}
