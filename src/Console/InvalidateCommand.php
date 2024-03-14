<?php

namespace Elysiumrealms\QueueExporter\Console;

use Elysiumrealms\QueueExporter\Models\QueueExport;
use Illuminate\Console\Command;

class InvalidateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue-exporter:invalidate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invalidate all of the expired exports';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Update null expires_at to now
        QueueExport::whereNull('expires_at')
            ->where('status', QueueExport::STATUS_COMPLETED)
            ->update(['expires_at' => now()
                ->addSeconds(config('queue-exporter.life_span'))]);

        // Delete all expired exports
        QueueExport::where('expires_at', '<=', now())->delete();
    }
}
