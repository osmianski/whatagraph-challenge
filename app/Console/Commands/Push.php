<?php

namespace App\Console\Commands;

use App\Jobs\PushLocation;
use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class Push extends Command
{
    protected $signature = 'whatagraph:push';

    protected $description = 'Push weather data to Whatagraph';

    public function handle(): int
    {
        foreach (Location::all(['id']) as $location) {
            Bus::dispatch(new PushLocation($location));
        }

        return Command::SUCCESS;
    }
}
