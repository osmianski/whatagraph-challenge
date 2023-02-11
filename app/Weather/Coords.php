<?php

namespace App\Weather;

class Coords
{
    public string $address;
    public float $latitude;
    public float $longitude;

    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        $instance->address = $data->name;
        $instance->latitude = $data->lat;
        $instance->longitude = $data->lon;

        return $instance;
    }
}
