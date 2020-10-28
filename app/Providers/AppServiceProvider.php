<?php

namespace App\Providers;

use App\Helpers\PackageManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // log DB queries
        DB::listen(function ($query) {
            Log::debug($query->sql, ['params' => $query->bindings, 'time' => $query->time]);
        });

        // Automatically set app.url variable when not running from a console
        if (!$this->app->runningInConsole()) {
            config(['app.url' => url('/')]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        $packageManager = new PackageManager();

        // if any extra packages are installed
        if (count($packageManager->getEnabled())) {
            // auto load package classes
            spl_autoload_register([$packageManager, 'autoload']);

            // register package service providers
            foreach ($packageManager->getEnabled() as $package) {
                foreach ($package->providers as $provider) {
                    $this->app->register($provider);
                }
            }
        }

        // PackageManager() instance can not be bound using $this->app->singleton(),
        // because all packages config files need to be properly loaded first,
        // which happens only after registering package service providers (see above).
        // But instead we can bind existing PackageManager instance, so that PackageManager class is not instantiated more than once.
        $this->app->instance(PackageManager::class, $packageManager->loadVersions());
    }
}
