<?php

namespace Stacht\Addresses;

use Illuminate\Support\ServiceProvider;
use Stacht\Addresses\Models\Address;

class AddressesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/stacht-addresses.php', 'stacht-addresses');

        // Register the service the package provides.
        $this->app->singleton('addresses', function ($app) {
            return new Address();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['addresses'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/stacht-addresses.php' => config_path('stacht-addresses.php'),
        ], 'config');

        // Publishing the migration file.
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');
    }
}
