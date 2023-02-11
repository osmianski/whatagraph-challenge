<?php

namespace App\Jobs;

use App\Enums\Daytime;
use App\Enums\YesNo;
use App\Exceptions\NotImplemented;
use App\Models\Location;
use App\Weather\Current;
use App\Weather\Forecast;
use App\Weather\Info;
use App\Weather\Weather;
use App\Whatagraph\DataPoint;
use App\Whatagraph\Whatagraph;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

        $this->geoLocate();

        $info = $this->weather->getInfo($this->location->latitude,
            $this->location->longitude, current: $this->current,
            forecast: $this->forecast);

        if ($this->current) {
            $this->pushCurrent($info->current);
        }

        if ($this->forecast) {
            foreach ($info->forecasts as $forecast) {
                $this->pushForecast($forecast);
            }
        }
    }

    protected function pushCurrent(Current $current): void
    {
        $daytime = $this->getDaytime($current);

        $this->pushDataPoint(DataPoint::new($current->datetime, [
            'is_forecast' => YesNo::No->value,
            'location' => $this->location->address,
            'daytime' => 'N/A',
            'pressure' => $current->pressure,
            'humidity' => $current->humidity,
        ]));

        $this->pushDataPoint(DataPoint::new($current->datetime, [
            'is_forecast' => YesNo::No->value,
            'location' => $this->location->address,
            'daytime' => $daytime->value,
            'temperature' => $current->temperature,
            'feels_like' => $current->feelsLike,
        ]));
    }

    protected function pushForecast(Forecast $forecast): void
    {
        $this->pushDataPoint(DataPoint::new($forecast->datetime, [
            'is_forecast' => YesNo::Yes->value,
            'location' => $this->location->address,
            'daytime' => 'N/A',
            'pressure' => $forecast->pressure,
            'humidity' => $forecast->humidity,
        ]));

        foreach ($forecast->daytimes as $daytimeForecast) {
            $this->pushDataPoint(DataPoint::new($forecast->datetime, [
                'is_forecast' => YesNo::Yes->value,
                'location' => $this->location->address,
                'daytime' => $daytimeForecast->daytime->value,
                'temperature' => $daytimeForecast->temperature,
                'feels_like' => $daytimeForecast->feelsLike,
            ]));
        }
    }

    protected function getDaytime(Current $current): Daytime
    {
        $hour = $current->datetime->hour;

        return match (true) {
            $hour >= 4 && $hour < 10 => Daytime::Morning,
            $hour >= 10 && $hour < 16 => Daytime::Day,
            $hour >= 16 && $hour < 22 => Daytime::Evening,
            default => Daytime::Night,
        };
    }

    protected function geoLocate(): void
    {
        if ($this->location->latitude !== null) {
            return;
        }

        $coords = $this->weather->getGeoCoords($this->location->address);
        $this->location->latitude = $coords->latitude;
        $this->location->longitude = $coords->longitude;
        $this->location->save();
    }

    protected function pushDataPoint(DataPoint $dataPoint): void
    {
        $cacheKey = $this->getCacheKey($dataPoint);
        if ($id = Cache::get($cacheKey)) {
            $this->whatagraph->updateDataPoint($id, $dataPoint);
        }
        else {
            Cache::put($cacheKey, $this->whatagraph->createDataPoint($dataPoint),
                now()->addDays(30));
        }
    }

    protected function getCacheKey(DataPoint $dataPoint): string
    {
        $dimensions = ['date' => $dataPoint->date->format('Y-m-d')];

        foreach (array_keys(config('whatagraph.dimensions')) as $dimension) {
            $dimensions[$dimension] = $dataPoint->data[$dimension];
        }
        ksort($dimensions);

        return sha1(json_encode($dimensions));
    }
}
