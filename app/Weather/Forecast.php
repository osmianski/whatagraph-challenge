<?php

namespace App\Weather;

use App\Enums\Daytime;
use App\Exceptions\NotImplemented;
use Illuminate\Support\Carbon;

class Forecast
{
    public Carbon $datetime;
    public int $pressure;
    public int $humidity;
    /**
     * @var array|DaytimeForecast[]
     */
    public array $daytimes;

    static public function fromApi(\stdClass $data, string $timezone): static
    {
        $instance = new static();

        $instance->datetime = Carbon::createFromTimestamp($data->dt, $timezone);

        $instance->pressure = $data->pressure;
        $instance->humidity = $data->humidity;

        $instance->daytimes = [
            new DaytimeForecast(Daytime::Morning,
                $data->temp->morn, $data->feels_like->morn),
            new DaytimeForecast(Daytime::Day,
                $data->temp->day, $data->feels_like->day),
            new DaytimeForecast(Daytime::Evening,
                $data->temp->eve, $data->feels_like->eve),
            new DaytimeForecast(Daytime::Night,
                $data->temp->night, $data->feels_like->night),
        ];

        return $instance;
    }
}
