<?php

namespace App\Http\Controllers\Auth;

use App\Models\SocialProfile;
use App\Models\User;
use App\Services\UserService;
use GuzzleHttp\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(StartSession::class);
    }

    public function url($provider)
    {
        // Socialite requires config to be loaded from config/services.php, so setting it manually, because it's stored in config/oauth.php
        config(['services.' . $provider => config('oauth.' . $provider)]);

        return [
            'url' => Socialite::driver($provider)->redirect()->getTargetUrl()
        ];
    }

    public function сallback(Request $request, $provider)
    {
        // retrieve user profile using OAuth
        try {
            // Socialite requires config to be loaded from config/services.php, so setting it manually, because it's stored in config/oauth.php
            config(['services.' . $provider => config('oauth.' . $provider)]);

            $providerUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            Log::error(sprintf('%s login error: %s', $provider, $e->getMessage()));
            return redirect('login');
        }

        // check if user with given email exists locally. If not - create it
        $userEmail = $providerUser->getEmail() ?: $providerUser->getId() . '_' . $providerUser->getNickname();

        if (strpos($userEmail, '@') === FALSE)
            $userEmail .= '@' . $provider . '.com';

        $userName = $providerUser->getName() ?: ($providerUser->getNickname() ?: $providerUser->getId());

        // if user with the same name exists add random number to the name
        if (User::where('name', $userName)->count()) {
            $userName .= ' ' . random_int(1000, 99999999);
        }

        $user = User::where('email', $userEmail)->first();

        if (!$user) {
            $user = UserService::create([
                'name'              => $userName,
                'email'             => $userEmail,
                'password'          => $providerUser->token,
                'email_verified_at' => Carbon::now(),
            ]);
        }

        // retrieve avatar if it's provided and user doesn't have one
        if (!$user->avatar && ($avatarUrl = $providerUser->getAvatar())) {
            try {
                $client = new Client();
                $response = $client->get($avatarUrl);
                if ($response->getStatusCode() == 200) {
                    $avatarFileName = $user->id . '_' . time() . '.jpg';
                    // save the avatar locally
                    if (Storage::disk('public')->put('avatars/' . $avatarFileName, $response->getBody()->getContents())) {
                        // bind the uploaded avatar to user
                        $user->avatar = $avatarFileName;
                        $user->save();
                    } else {
                        Log::error(sprintf('Can not save avatar to %s', $avatarFileName));
                    }
                } else {
                    Log::error(sprintf('Can not retrieve remote avatar %s', $avatarUrl));
                }
            } catch (\Exception $e) {
                Log::error(sprintf('OAuth avatar capture: %s', $e->getMessage()));
            }
        }

        // throw Registered event if user just signed up
        if ($user->wasRecentlyCreated) {
            event(new Registered($user));
        }

        // check if social profile exists and create a new one if not. User can have multiple social profiles linked (Facebook, Google etc)
        $socialProfile = SocialProfile::firstOrCreate(
            ['provider_name' => $provider, 'provider_user_id' => $providerUser->getId()],
            ['user_id' => $user->id]
        );

        // authenticate user
        auth()->guard()->login($user, TRUE);

        return view('oauth/callback', [
            'user' => UserService::user($user)
        ]);
    }
}
