<?php

namespace Railroad\LeadTracker\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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

        if (config('lead-tracker.data_mode') == 'host') {
            $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        }
    }
}
