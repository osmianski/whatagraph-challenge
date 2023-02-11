<?php

namespace App\Weather;

use App\Exceptions\NotImplemented;
use Illuminate\Support\Collection;

class Info
{
    public ?Current $current;
    public ?Collection $forecasts;

    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        $instance->current = isset($data->current)
            ? Current::fromApi($data->current)
            : null;

        $instance->forecasts = isset($data->daily)
            ? collect($data->daily)
                ->map(fn (\stdClass $day) => Forecast::fromApi($day))
            : null;

        return $instance;
    }

}
