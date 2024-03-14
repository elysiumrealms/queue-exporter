<?php

namespace Elysiumrealms\QueueExporter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue-exporter:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the QueueExporter resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Dependencies
        $this->call('sql-interceptor:install');

        $this->comment('Publishing QueueExporter Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'queue-exporter-provider']);

        $this->comment('Publishing QueueExporter Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'queue-exporter-config']);

        $this->comment('Publishing QueueExporter Migrations...');
        $this->callSilent('vendor:publish', ['--tag' => 'queue-exporter-migrations']);

        $this->registerQueueExporterStorage();
        $this->registerQueueExporterServiceProvider();

        $this->info('QueueExporter scaffolding installed successfully.');
    }

    /**
     * Register the QueueExporter storage disk in the application configuration file.
     *
     * @return void
     */
    protected function registerQueueExporterStorage()
    {
        if (config('filesystems.disks.exports')) {
            return;
        }

        $filesystemsConfig = file_get_contents(config_path('filesystems.php'));

        file_put_contents(config_path('filesystems.php'), str_replace(
            "    'disks' => [" . PHP_EOL,
            "    'disks' => [" . PHP_EOL  . PHP_EOL .
                "        'exports' => [" . PHP_EOL .
                "            'driver' => 'local'," . PHP_EOL .
                "            'root' => public_path('exports')," . PHP_EOL .
                "            'visibility' => 'public'," . PHP_EOL .
                "            'url' => env('APP_URL') . '/exports'," . PHP_EOL .
                "        ]," . PHP_EOL,
            $filesystemsConfig
        ));
    }

    /**
     * Register the QueueExporter service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerQueueExporterServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace . '\\Providers\\QueueExporterServiceProvider::class')) {
            return;
        }

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class," . PHP_EOL,
            "{$namespace}\\Providers\EventServiceProvider::class," . PHP_EOL . "        {$namespace}\Providers\QueueExporterServiceProvider::class," . PHP_EOL,
            $appConfig
        ));
    }
}
