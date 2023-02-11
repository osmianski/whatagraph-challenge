<?php

namespace App\Whatagraph;

use App\Exceptions\NotImplemented;

class Metric implements \JsonSerializable
{
    public ?int $id = null;
    public string $name;
    public string $title;
    public Metric\Type $type;
    public Metric\Accumulator $accumulator;
    public bool $negativeRatio = false;

    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        $instance->id = $data->id;
        $instance->name = $data->external_id;
        $instance->title = $data->name;
        $instance->type = Metric\Type::from($data->type);
        $instance->accumulator = Metric\Accumulator::from($data->options->accumulator);
        $instance->negativeRatio = $data->negative_ratio;

        return $instance;
    }

    static public function fromConfig(string $key, array $data): static
    {
        $instance = new static();

        $instance->name = $key;
        $instance->title = $data['title'];
        $instance->type = $data['type'];
        $instance->accumulator = $data['accumulator'];
        if (array_key_exists('negative_ratio', $data)) {
            $instance->negativeRatio = $data['negative_ratio'];
        }

        return $instance;
    }

    public function jsonSerialize(): mixed
    {
        return (object)[
            'external_id' => $this->name,
            'name' => $this->title,
            'type' => $this->type->value,
            'accumulator' => $this->accumulator->value,
            'negative_ratio' => $this->negativeRatio,
        ];
    }
}
