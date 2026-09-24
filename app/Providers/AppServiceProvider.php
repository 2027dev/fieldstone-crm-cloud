<?php

namespace App\Providers;

use App\Support\SetupProgress;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        View::composer('components.app-layout', function ($view): void {
            $view->with('setupRemaining', $this->app->make(SetupProgress::class)->remainingCount());
        });
    }
}
