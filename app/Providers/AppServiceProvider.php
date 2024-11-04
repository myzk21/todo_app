<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Google\Client as Google_Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Google_Client::class, function () {
            return new Google_Client([
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret')
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // HTTPSリダイレクトを強制
        if ($this->app->environment()) {
            URL::forceScheme('https');
        }
    }
}
