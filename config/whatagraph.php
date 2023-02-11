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
        'temperature' => [
            'title' => 'Temperature',
            'type' => Metric\Type::Float_,
            'accumulator' => Metric\Accumulator::Last,
            'negative_ratio' => false,
        ],
    ],
];
