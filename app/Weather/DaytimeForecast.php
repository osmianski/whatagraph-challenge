<?php

namespace App\Weather;

use App\Enums\Daytime;

class DaytimeForecast
{
    public function __construct(public Daytime $daytime,
        public float $temperature, public float $feelsLike)
    {
    }
}
