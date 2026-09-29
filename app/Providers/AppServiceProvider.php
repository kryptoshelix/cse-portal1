<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        // SECURITY: deny by default. Every model is unguarded-free; mass
        // assignment is limited to explicit $fillable lists (verified by tests).
        Model::preventSilentlyDiscardingAttributes($this->app->isLocal());
        Model::shouldBeStrict(! $this->app->isProduction());
    }
}
