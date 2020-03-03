<?php

namespace Railroad\LeadTracker\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Redis;

class LeadTrackerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->publishes(
            [
                __DIR__ . '/../../config/lead-tracker.php' => config_path('lead-tracker.php'),
            ]
        );

        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }
}
