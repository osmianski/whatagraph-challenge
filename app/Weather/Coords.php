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

    static public function new(string $address, float $latitude, float $longitude): static {
        $instance = new static();

        $instance->address = $address;
        $instance->latitude = $latitude;
        $instance->longitude = $longitude;

        return $instance;
    }
}
