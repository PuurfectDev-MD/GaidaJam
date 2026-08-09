<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use App\Providers\HackClubProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Socialite::extend('hackclub', function ($app) {
        $config = config('services.hackclub');
        return Socialite::buildProvider(HackClubProvider::class, $config);
    });
    }
}
