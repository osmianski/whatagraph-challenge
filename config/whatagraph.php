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
        'daytime' => [
            'title' => 'Daytime',
            'type' => Dimension\Type::String_,
        ],
        'is_forecast' => [
            'title' => 'Is Forecast',
            'type' => Dimension\Type::String_,
        ],
    ],

    'metrics' => [
        'temperature' => [
            'title' => 'Temperature',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
        'feels_like' => [
            'title' => 'Feels Like',
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
        'humidity' => [
            'title' => 'Humidity',
            'type' => Metric\Type::Int_,
            'accumulator' => Metric\Accumulator::Average,
            'negative_ratio' => false,
        ],
    ],
];
