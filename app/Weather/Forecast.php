<?php

namespace App\Weather;

use App\Exceptions\NotImplemented;
use Illuminate\Support\Carbon;

class Forecast
{
    public Carbon $datetime;
    public float $dayTemperature;

    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        $instance->datetime = Carbon::createFromTimestamp($data->dt);
        $instance->dayTemperature = $data->temp->day;

        return $instance;
    }
}
