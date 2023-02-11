<?php

namespace App\Console\Commands;

use App\Models\Location;
use Illuminate\Console\Command;

class Push extends Command
{
    protected $signature = 'whatagraph:push';

    protected $description = 'Push weather data to Whatagraph';

    public function handle(): int
    {
        foreach (Location::all(['id']) as $location) {
        }

        return Command::SUCCESS;
    }
}
