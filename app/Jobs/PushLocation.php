<?php

namespace App\Jobs;

use App\Enums\Daytime;
use App\Enums\YesNo;
use App\Exceptions\NotImplemented;
use App\Models\Location;
use App\Weather\Coords;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PushLocation implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 10;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public int $backoff = 60 * 30; // 30 minutes

    protected string $location;
    protected Weather $weather;
    protected Whatagraph $whatagraph;
    protected bool $forecast;
    /**
     * @var array|DataPoint[]
     */
    protected array $dataPoints = [];

    public function __construct(string $location, Weather $weather = null,
        Whatagraph $whatagraph = null, bool $forecast = true)
    {
        $this->location = $location;
        $this->weather = $weather ?? new Weather();
        $this->whatagraph = $whatagraph ?? new Whatagraph();
        $this->forecast = $forecast;
    }

    public function uniqueId()
    {
        return $this->location;
    }

    public function handle(): void
    {
        $info = $this->getInfo();

        $this->prepareCurrent($info->current);

        if ($this->forecast) {
            foreach ($info->forecasts as $forecast) {
                if (!$this->isTodayForecast($forecast, $info)) {
                    $this->prepareForecast($forecast);
                }
            }
        }

        $this->pushDataPoints();
    }

    protected function prepareCurrent(Current $current): void
    {
        $data = [
            'location' => $this->location,
            'pressure' => $current->pressure,
            'humidity' => $current->humidity,
        ];

        if ($prefix = $this->getDaytimePrefix($current->datetime)) {
            $data["{$prefix}temperature"] = $current->temperature;
            $data["{$prefix}feels_like"] = $current->feelsLike;
        }

        $this->prepareDataPoint($current->datetime, $data);
    }

    protected function prepareForecast(Forecast $forecast): void
    {
        $this->prepareDataPoint($forecast->datetime, [
            'location' => $this->location,
            'pressure_forecast' => $forecast->pressure,
            'humidity_forecast' => $forecast->humidity,
            'day_temperature_forecast' =>
                $forecast->daytimes[Daytime::Day->value]->temperature,
            'night_temperature_forecast' =>
                $forecast->daytimes[Daytime::Night->value]->temperature,
            'day_feels_like_forecast' =>
                $forecast->daytimes[Daytime::Day->value]->feelsLike,
            'night_feels_like_forecast' =>
                $forecast->daytimes[Daytime::Night->value]->feelsLike,
        ]);
    }

    protected function getDaytime(Carbon $datetime): Daytime
    {
        $hour = $datetime->hour;

        return match (true) {
            $hour >= 4 && $hour < 10 => Daytime::Morning,
            $hour >= 10 && $hour < 16 => Daytime::Day,
            $hour >= 16 && $hour < 22 => Daytime::Evening,
            default => Daytime::Night,
        };
    }

    protected function getDaytimePrefix(Carbon $datetime): ?string
    {
        return match ($this->getDaytime($datetime)) {
            Daytime::Day => 'day_',
            Daytime::Night => 'night_',
            default => null,
        };
    }

    protected function geoLocate(): Coords
    {
        return Cache::remember(
            "coords:{$this->location}",
            now()->addDays(30),
            fn() => $this->weather->getGeoCoords($this->location)
        );
    }

    protected function prepareDataPoint(Carbon $datetime, array $data): void
    {
        $date = $datetime->format('Y-m-d');
        if ($dataPoint = $this->dataPoints[$date] ?? null) {
            $dataPoint->data = array_merge($dataPoint->data, $data);
            return;
        }

        $dataPoint = DataPoint::new($datetime, $data);
        $dataPoint->id = Cache::tags('id')
            ->get("id:{$this->location},$date");
        $this->dataPoints[$date] = $dataPoint;
    }

    protected function pushDataPoints(): void
    {
        /* @var DataPoint[] $dataPoints */
        $dataPoints = collect($this->dataPoints)->whereNull('id')->toArray();
        if (!empty($dataPoints)) {
            $ids = $this->whatagraph->createDataPoints($dataPoints);
            foreach ($ids as $date => $id) {
                Cache::tags('id')
                    ->put("id:{$this->location},$date", $id,
                        now()->addDays(30));
            }
        }

        $dataPoints = collect($this->dataPoints)->whereNotNull('id')->toArray();
        foreach ($dataPoints as $dataPoint) {
            $this->whatagraph->updateDataPoint($dataPoint->id, $dataPoint);
        }
    }

    /**
     * @return Info
     */
    protected function getInfo(): Info
    {
        $coords = $this->geoLocate();

        return $this->weather->getInfo($coords->latitude,
            $coords->longitude, forecast: $this->forecast);
    }

    protected function isTodayForecast(Forecast $forecast, Info $info): bool
    {
        return $forecast->datetime->format('Y-m-d') ===
            $info->current->datetime->format('Y-m-d');
    }
}
