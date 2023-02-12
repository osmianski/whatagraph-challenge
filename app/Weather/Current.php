<?php

namespace App\Weather;

use Illuminate\Support\Carbon;

class Current
{
    public Carbon $datetime;
    public float $temperature;
    public float $feelsLike;
    public int $pressure;
    public int $humidity;


    static public function fromApi(\stdClass $data, string $timezone): static
    {
        $instance = new static();

        $instance->datetime = Carbon::createFromTimestamp($data->dt, $timezone);
        $instance->temperature = $data->temp;
        $instance->feelsLike = $data->feels_like;
        $instance->pressure = $data->pressure;
        $instance->humidity = $data->humidity;

        return $instance;
    }

    static public function new(Carbon $datetime, float $temperature,
        float $feelsLike, int $pressure, int $humidity): static
    {
        $instance = new static();

        $instance->datetime = $datetime;
        $instance->temperature = $temperature;
        $instance->feelsLike = $feelsLike;
        $instance->pressure = $pressure;
        $instance->humidity = $humidity;

        return $instance;
    }
}
