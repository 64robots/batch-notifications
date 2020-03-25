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
        $time = time();

        if (!class_exists('CreateBatchNotificationTables')) {
            $migrationFileName = $this->getMigrationFilename('create_batch_notification_tables', $time);
            $this->publishes([
                __DIR__.'/../migrations/2018_09_04_205640_create_batch_notification_tables.php' => $migrationFileName,
            ], 'migrations');
        }

        $this->commands([
            Console\DispatchBatchNotifications::class
        ]);
    }

    protected function getMigrationFileName($migrationName, $time)
    {
        $timestamp = date('Y_m_d_His', $time);

        return $this->app->databasePath() . "/migrations/{$timestamp}_{$migrationName}.php";
    }
}