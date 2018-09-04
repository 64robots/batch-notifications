<?php

namespace R64\BatchNotifications;

use Illuminate\Support\ServiceProvider;

class BatchNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../migrations/2018_09_04_205640_create_batch_notification_tables.php' => base_path('database/migrations'),
            ]);

            $this->commands([
                Console\DispatchBatchNotifications::class
            ]);
        }
    }
}