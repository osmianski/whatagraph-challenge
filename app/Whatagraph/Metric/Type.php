<?php

namespace App\Whatagraph\Metric;

enum Type: string
{
    case Int_ = 'int';
    case Float_ = 'float';
    case Currency = 'currency';
}
