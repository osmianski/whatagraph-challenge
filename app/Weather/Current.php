<?php

namespace App\Weather;

use App\Exceptions\NotImplemented;
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
}
