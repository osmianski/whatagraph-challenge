<?php

namespace App\Enums;

enum Daytime: string
{
    case Night = 'Night';
    case Morning = 'Morning';
    case Day = 'Day';
    case Evening = 'Evening';
}
