<?php

namespace App\Jobs;

use App\Exceptions\NotImplemented;
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
    private bool $current;
    private bool $forecast;

    public function __construct(Location $location, Weather $weather = null,
        Whatagraph $whatagraph = null, bool $current = true,
        bool $forecast = true)
    {
        $this->location = $location;
        $this->weather = $weather ?? new Weather();
        $this->whatagraph = $whatagraph ?? new Whatagraph();
        $this->current = $current;
        $this->forecast = $forecast;
    }

    public function uniqueId()
    {
        return $this->location->id;
    }

    public function handle(): void
    {
        if (!$this->current && !$this->forecast) {
            return;
        }

        if ($this->location->latitude === null || $this->location->longitude === null) {
            $coords = $this->weather->getGeoCoords($this->location->address);
            $this->location->latitude = $coords->latitude;
            $this->location->longitude = $coords->longitude;
            $this->location->save();
        }

        $info = $this->weather->getInfo($this->location->latitude,
            $this->location->longitude, current: $this->current,
            forecast: $this->forecast);

        if ($this->current) {
            $this->pushCurrent($info->current);
        }

        if ($this->forecast) {
            $this->pushForecasts($info->forecasts);
        }
    }

    protected function pushCurrent(?\App\Weather\Current $current): void
    {
        throw new NotImplemented();
    }

    protected function pushForecasts(?\Illuminate\Support\Collection $forecasts): void
    {
        throw new NotImplemented();
    }
}
