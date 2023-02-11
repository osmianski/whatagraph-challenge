<?php

namespace App\Weather;

use App\Exceptions\NotImplemented;

class Forecast
{
    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        throw new NotImplemented();

        return $instance;
    }

}
