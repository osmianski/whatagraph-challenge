<?php

use App\Whatagraph\Dimension;
use App\Whatagraph\Metric;

return [
    'api_key' => env('WHATAGRAPH_API_KEY'),

    'dimensions' => [
        'location' => [
            'title' => 'Location',
            'type' => Dimension\Type::String_,
        ],
    ],

    'metrics' => [
        'day_temperature' => [
            'title' => 'Day Temperature',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'day_temperature_forecast' => [
            'title' => 'Day Temperature Forecast',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'night_temperature' => [
            'title' => 'Night Temperature',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'night_temperature_forecast' => [
            'title' => 'Night Temperature Forecast',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'day_feels_like' => [
            'title' => 'Day Feels Like',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'day_feels_like_forecast' => [
            'title' => 'Day Feels Like Forecast',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'night_feels_like' => [
            'title' => 'Night Feels Like',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'night_feels_like_forecast' => [
            'title' => 'Night Feels Like Forecast',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'pressure' => [
            'title' => 'Pressure',
            'type' => Metric\Type::Int_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'pressure_forecast' => [
            'title' => 'Pressure Forecast',
            'type' => Metric\Type::Int_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'humidity' => [
            'title' => 'Humidity',
            'type' => Metric\Type::Int_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'humidity_forecast' => [
            'title' => 'Humidity Forecast',
            'type' => Metric\Type::Int_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
    ],
];
