<?php

namespace Elysiumrealms\QueueExporter;

use Illuminate\Support\ServiceProvider;

class QueueExporterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $this->publishes([
                __DIR__ . '/../config/queue-exporter.php' => config_path('queue-exporter.php'),
            ], 'queue-exporter-config');
        }

        $this->offerPublishing();
        $this->registerCommands();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/queue-exporter.php', 'queue-exporter');
    }

    /**
     * Setup the resource publishing groups for SQLInterceptor.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../stubs/QueueExporterServiceProvider.stub' =>
                app_path('Providers/QueueExporterServiceProvider.php'),
            ], 'queue-exporter-provider');
        }
    }

    /**
     * Register the SQLInterceptor Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }

        $this->commands([
            Console\InvalidateCommand::class,
        ]);
    }
}
