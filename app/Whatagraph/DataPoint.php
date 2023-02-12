<?php

namespace App\Whatagraph;

use App\Enums\Daytime;
use App\Exceptions\NotImplemented;
use App\Weather\Current;
use Illuminate\Support\Carbon;

class DataPoint implements \JsonSerializable
{
    public ?string $id;
    public Carbon $date;

    public array $data;

    static public function new(Carbon $date, array $data): static
    {
        $instance = new static();

        $instance->date = $date;
        $instance->data = $data;

        return $instance;
    }

    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        $instance->id = $data->id;
        $instance->date = Carbon::createFromFormat('Y-m-d', $data->date);
        $instance->data = (array)$data->integration_data;

        return $instance;
    }

    public function jsonSerialize(): mixed
    {
        return (object)array_merge(
            $this->data,
            ['date' => $this->date->format('Y-m-d')]
        );
    }
}
