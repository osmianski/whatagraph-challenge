<?php

namespace App\Weather;

use App\Exceptions\NotImplemented;
use Illuminate\Support\Carbon;

class Current
{
    public Carbon $datetime;
    public float $temperature;

    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        $instance->datetime = Carbon::createFromTimestamp($data->dt);
        $instance->temperature = $data->temp;

        return $instance;
    }
}
