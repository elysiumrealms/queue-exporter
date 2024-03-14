<?php

namespace Elysiumrealms\QueueExporter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Dcat\EasyExcel\Excel;
use Elysiumrealms\QueueExporter\Models\QueueExport;

class ExportExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $titles = [];

    /**
     * @var QueueExport
     */
    protected $queueExport;

    /**
     * @var array
     */
    protected $queries;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        QueueExport $queueExport,
        array $queries,
        array $titles
    ) {
        $this->queries = $queries;
        $this->titles = $titles;
        $this->queueExport = $queueExport;
        $this->queue = config('queue-exporter.queue');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->queueExport->status = QueueExport::STATUS_PROCESSING;
        $this->queueExport->save();

        $result = collect($this->queries)
            ->map(function ($query) {
                // Execute the query with bindings
                return DB::connection()
                    ->select($query['query'], $query['bindings']);
            });

        $exporter = Excel::export()
            ->disk('exports')
            ->headings($this->titles);

        $result = $result->flatMap(function ($data) {
            return collect($data)
                ->map(function ($item) {
                    return collect($item)->toArray();
                });
        })->toArray();

        $exporter->data($result);
        // Export the result to exports disk
        $exporter->store($this->queueExport->filename);

        $this->queueExport->status = QueueExport::STATUS_COMPLETED;
        $this->queueExport->save();
    }
}
