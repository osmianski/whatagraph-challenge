<?php

namespace App\Whatagraph\Metric;

enum Accumulator: string
{
    case Sum = 'sum';
    case Average = 'average';
    case Last = 'last';
}
