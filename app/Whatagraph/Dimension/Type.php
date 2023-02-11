<?php

namespace App\Whatagraph\Dimension;

enum Type: string
{
    case String_ = 'string';
    case Int_ = 'int';
    case Time = 'time';
    case Float_ = 'float';
    case Date = 'date';
}
