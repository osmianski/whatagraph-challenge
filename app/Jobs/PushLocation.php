<?php

namespace App\Jobs;

use App\Models\Location;
use App\Weather\Weather;
use App\Whatagraph\Whatagraph;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushLocation implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Location $location;
    protected Weather $weather;
    protected Whatagraph $whatagraph;

    public function __construct(Location $location, Weather $weather = null,
        Whatagraph $whatagraph = null)
    {
        $this->location = $location;
        $this->weather = $weather ?? new Weather();
        $this->whatagraph = $whatagraph ?? new Whatagraph();
    }

    public function uniqueId()
    {
        return $this->location->id;
    }

    public function handle(): void
    {
        if ($this->location->latitude === null || $this->location->longitude === null) {
            $coords = $this->weather->getGeoCoords($this->location->address);
            $this->location->latitude = $coords->latitude;
            $this->location->longitude = $coords->longitude;
            $this->location->save();
        }

        $forecast = $this->weather->getForecast($this->location->latitude,
            $this->location->longitude);
    }
}
