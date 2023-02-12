<?php

namespace App\Console\Commands;

use App\Whatagraph\Dimension;
use App\Whatagraph\Metric;
use App\Whatagraph\Whatagraph;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Init extends Command
{
    protected $signature = 'whatagraph:init {--fresh : Delete existing data}';

    protected $description = 'Create metrics/dimensions in Whatagraph';

    protected ?Whatagraph $whatagraph;

    public function __construct(Whatagraph $whatagraph = null)
    {
        parent::__construct();

        $this->whatagraph = $whatagraph ?? new Whatagraph();
    }

    public function handle(): int
    {
        $this->initMetrics();
        $this->initDimensions();

        if ($this->option('fresh')) {
            $this->deleteExistingData();
        }

        return Command::SUCCESS;
    }

    protected function initMetrics(): void
    {
        $oldMetrics = $this->whatagraph->getMetrics();

        foreach (config('whatagraph.metrics') as $key => $value) {
            $metric = Metric::fromConfig($key, $value);

            if ($oldMetric = $oldMetrics[$metric->name] ?? null) {
                $this->whatagraph->updateMetric($oldMetric->id, $metric);
                unset($oldMetrics[$metric->name]);
            }
            else {
                $this->whatagraph->createMetric($metric);
            }
        }

        foreach ($oldMetrics as $oldMetric) {
            $this->whatagraph->deleteMetric($oldMetric->id);
        }
    }

    protected function initDimensions(): void
    {
        $oldDimensions = $this->whatagraph->getDimensions();

        foreach (config('whatagraph.dimensions') as $key => $value) {
            $dimension = Dimension::fromConfig($key, $value);

            if ($oldDimension = $oldDimensions[$dimension->name] ?? null) {
                $this->whatagraph->updateDimension($oldDimension->id, $dimension);
                unset($oldDimensions[$dimension->name]);
            }
            else {
                $this->whatagraph->createDimension($dimension);
            }
        }

        foreach ($oldDimensions as $oldDimension) {
            $this->whatagraph->deleteDimension($oldDimension->id);
        }
    }

    protected function deleteExistingData(): void
    {
        $dataPoints = $this->whatagraph->getDataPoints();
        while ($dataPoints->isNotEmpty()) {
            foreach ($this->whatagraph->getDataPoints() as $dataPoint) {
                $this->whatagraph->deleteDataPoint($dataPoint->id);
            }

            $dataPoints = $this->whatagraph->getDataPoints();
        }

        Cache::tags('id')->flush();
    }
}
