<?php

namespace Elysiumrealms\QueueExporter\Admin\Grid\Exporters;

use Dcat\Admin\Grid;
use Dcat\EasyExcel\Excel;
use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Elysiumrealms\QueueExporter\Jobs\ExportExcel;
use Elysiumrealms\QueueExporter\Models\QueueExport;
use Elysiumrealms\SQLInterceptor\SQLInterceptor;
use Illuminate\Support\Facades\Route;

class ExcelExporter extends AbstractExporter
{
    /**
     * @var string|null
     */
    protected $route = 'queue-exporter.exports';

    public function __construct($titles = [])
    {
        parent::__construct($titles);

        if (!class_exists(Excel::class)) {
            throw new \Exception('To use exporter, please install [dcat/easy-excel] first.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function export()
    {
        $filename = $this->getFilename() . '.' . $this->extension;

        $queries = SQLInterceptor::intercept(function () {
            $exporter = Excel::export();

            if ($this->scope === Grid\Exporter::SCOPE_ALL) {
                $exporter->chunk(function (int $times) {
                    return $this->buildData($times);
                });
            } else {
                $exporter->data($this->buildData() ?: [[]]);
            }
        })->queries();

        $queueExport = QueueExport::create(['filename' => $filename]);

        dispatch(new ExportExcel($queueExport, $queries, $this->titles));

        return Route::has($this->route) ? exit
            : redirect()->route($this->route, ['id' => $queueExport->id]);
    }

    /**
     * Set the route to redirect to after the export is complete.
     *
     * @param string $route
     *
     * @return $this
     */
    public function route(string $route)
    {
        $this->route = $route;

        return $this;
    }
}
