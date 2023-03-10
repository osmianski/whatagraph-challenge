<?php

namespace App\Console\Commands;

use App\Jobs\PushLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class Push extends Command
{
    protected $signature = 'whatagraph:push {--current : Push current weather without forecasts }';

    protected $description = 'Push weather data to Whatagraph';

    public function handle(): int
    {
        foreach (config('locations') as $location) {
            Bus::dispatch(new PushLocation($location,
                forecast: !$this->option('current')));
        }

        return Command::SUCCESS;
    }
}
