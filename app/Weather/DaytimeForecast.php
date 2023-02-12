<?php

namespace App\Weather;

class DaytimeForecast
{
    public function __construct(public float $temperature, public float $feelsLike)
    {
    }
}
