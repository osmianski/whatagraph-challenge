<?php

namespace App\Weather;

use App\Enums\Daytime;
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
            Daytime::Morning->value => new DaytimeForecast(
                $data->temp->morn, $data->feels_like->morn),
            Daytime::Day->value => new DaytimeForecast(
                $data->temp->day, $data->feels_like->day),
            Daytime::Evening->value => new DaytimeForecast(
                $data->temp->eve, $data->feels_like->eve),
            Daytime::Night->value => new DaytimeForecast(
                $data->temp->night, $data->feels_like->night),
        ];

        return $instance;
    }

    static public function new(Carbon $datetime, int $pressure, int $humidity,
        array $daytimes): static
    {
        $instance = new static();

        $instance->datetime = $datetime;
        $instance->pressure = $pressure;
        $instance->humidity = $humidity;
        $instance->daytimes = $daytimes;

        return $instance;
    }
}
