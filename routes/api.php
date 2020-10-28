<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// STATIC PAGES
Route::prefix('pages')
    ->group(function () {
        Route::get('{page}', 'PageController@show');
    });

Route::prefix('public')
    ->group(function () {
        Route::get('games/recent', 'PageController@recentGames');
    });

// PUBLIC AUTHENTICATION ROUTES
Route::namespace('Auth')
    ->prefix('auth')
    ->middleware(['guest', 'maintenance'])
    ->group(function () {
        Route::post('login', 'LoginController@login');
        Route::post('register', 'RegisterController@register');
        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/reset', 'ResetPasswordController@reset');
    });

// EMAIL VERIFICATION ROUTES
if (config('settings.email_verification')) {
    Route::namespace('Auth')
        ->prefix('email')
        ->middleware(['auth:sanctum', 'maintenance', 'active'])
        ->group(function () {
            Route::post('verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
            Route::post('resend', 'VerificationController@resend');
        });
}

// PRIVATE AUTHENTICATION ROUTES
Route::namespace('Auth')
    ->prefix('auth')
    ->middleware(['auth:sanctum', 'maintenance', 'active'])
    ->group(function () {
        Route::post('logout', 'LoginController@logout');
    });

// PUBLIC OAUTH (SOCIAL LOGIN) ROUTES
Route::namespace('Auth')
    ->prefix('oauth')
    ->middleware(['guest', 'maintenance', 'social'])
    ->group(function () {
        Route::post('{provider}/url', 'OAuthController@url');
        Route::get('{provider}/callback', 'OAuthController@сallback');
    });

// PRIVATE USER ROUTES
Route::namespace('User')
    ->prefix('user')
    ->middleware(['auth:sanctum', 'maintenance', 'verified', 'active', '2fa'])
    ->group(function () {
        // user profile
        Route::get('', 'UserController@show')->name('user');
        Route::post('update', 'UserController@update');
        // password
        Route::patch('security/password/update', 'PasswordController@update');
        // two-factor auth
        Route::post('security/2fa/enable', 'TwoFactorAuthController@enable');
        Route::post('security/2fa/confirm', 'TwoFactorAuthController@confirm');
        Route::post('security/2fa/verify', 'TwoFactorAuthController@verify')->name('user.security.2fa.verify');
        Route::post('security/2fa/disable', 'TwoFactorAuthController@disable');
        // affiliate
        Route::get('affiliate/stats', 'AffiliateController@stats');
        Route::get('affiliate/commissions', 'AffiliateController@commissions');
        // account transactions
        Route::get('account/transactions', 'AccountController@transactions');
        // games
        Route::post('games/create', 'GameController@create')->name('user.games.create');
    });

// PUSHER AUTHENTICATION
Broadcast::routes(['middleware' => ['auth:sanctum', 'maintenance', 'verified', 'active', '2fa']]);

// OTHER PAGES FOR AUTHENTICATED USERS
Route::middleware(['auth:sanctum', 'maintenance', 'verified', 'active', '2fa'])
    ->group(function () {
        // users
        Route::get('users/{user}', 'User\UserController@profile');
        // history
        Route::get('history/user', 'HistoryController@user');
        Route::get('history/recent', 'HistoryController@recent');
        Route::get('history/wins', 'HistoryController@wins');
        Route::get('history/losses', 'HistoryController@losses');
        Route::get('history/games/{game}', 'HistoryController@show');
        Route::get('history/games/{game}/package', 'HistoryController@package');
        Route::get('history/games/{game}/details', 'HistoryController@details');
        Route::get('history/games/{game}/verify', 'HistoryController@verify');
        // leaderboard
        Route::get('leaderboard', 'LeaderboardController@index');
        // chat
        Route::get('chat/rooms', 'ChatController@getRooms');
        Route::get('chat/{room}', 'ChatController@getMessages');
        Route::post('chat/{room}', 'ChatController@sendMessage');
        // multiplayer game rooms
        Route::get('games/{packageId}/rooms', 'GameRoomController@index');
        Route::post('games/{packageId}/rooms', 'GameRoomController@create');
        Route::post('games/{packageId}/rooms/join', 'GameRoomController@join')->middleware('room.lock');
        Route::post('games/{packageId}/rooms/leave', 'GameRoomController@leave')->middleware('room.lock');
    });

// ADMIN ROUTES
Route::namespace('Admin')
    ->prefix('admin')
    ->middleware(['auth:sanctum', 'verified', 'active', '2fa', 'role:' . App\Models\User::ROLE_ADMIN])
    ->group(function () {
        // dashboard
        Route::get('dashboard/data/{key}', 'DashboardController@getData');
        // users
        Route::get('users', 'UserController@index');
        Route::get('users/{user}', 'UserController@show');
        Route::patch('users/{user}', 'UserController@update');
        Route::delete('users/{user}', 'UserController@destroy');
        Route::post('users/{user}/mail', 'UserController@mail');
        // accounts
        Route::get('accounts', 'AccountController@index');
        Route::get('accounts/{account}', 'AccountController@show');
        Route::post('accounts/{account}/debit', 'AccountController@debit');
        Route::post('accounts/{account}/credit', 'AccountController@credit');
        Route::get('accounts/{account}/transactions', 'AccountController@transactions');
        // games
        Route::get('games', 'GameController@index');
        Route::get('games/{game}', 'GameController@show');
        Route::get('games/{game}/package', 'GameController@package');
        Route::get('games/{game}/details', 'GameController@details');
        // affiliate
        Route::get('affiliate/tree', 'AffiliateController@tree');
        Route::get('affiliate/commissions', 'AffiliateController@commissions');
        Route::get('affiliate/commissions/{commission}', 'AffiliateController@show');
        Route::post('affiliate/commissions/{commission}/approve', 'AffiliateController@approve');
        Route::post('affiliate/commissions/{commission}/reject', 'AffiliateController@reject');
        // chat
        Route::resource('chat/rooms', 'ChatRoomController')->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::resource('chat/messages', 'ChatMessageController')->only(['index', 'destroy']);
        // settings
        Route::get('settings', 'SettingController@get');
        Route::post('settings', 'SettingController@update');
        // file uploads
        Route::post('files', 'FileController@store')->name('files.store');
        // maintenance
        Route::get('maintenance', 'MaintenanceController@index');
        Route::post('maintenance/up', 'MaintenanceController@up');
        Route::post('maintenance/down', 'MaintenanceController@down');
        Route::post('maintenance/command', 'MaintenanceController@command');
        Route::post('maintenance/migrate', 'MaintenanceController@migrate');
        Route::post('maintenance/cache', 'MaintenanceController@cache');
        Route::post('maintenance/cron', 'MaintenanceController@cron');
        // add-ons
        Route::get('add-ons', 'AddonController@index');
        Route::get('add-ons/{packageId}', 'AddonController@get');
        Route::get('add-ons/{packageId}/changelog', 'AddonController@changelog');
        Route::post('add-ons/{packageId}/enable', 'AddonController@enable');
        Route::post('add-ons/{packageId}/disable', 'AddonController@disable');
        Route::post('add-ons/{packageId}/install', 'AddonController@install');
        // license
        Route::get('license', 'LicenseController@index')->name('license.index');
        Route::post('license', 'LicenseController@register')->name('license.register');
    });
