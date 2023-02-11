<?php

namespace App\Whatagraph;

use App\Exceptions\NotImplemented;

class Dimension implements \JsonSerializable
{
    public ?int $id = null;
    public string $name;
    public string $title;
    public Dimension\Type $type;

    static public function fromApi(\stdClass $data): static
    {
        $instance = new static();

        $instance->id = $data->id;
        $instance->name = $data->external_id;
        $instance->title = $data->name;
        $instance->type = Dimension\Type::from($data->type);

        return $instance;
    }

    static public function fromConfig(string $key, array $data): static
    {
        $instance = new static();

        $instance->name = $key;
        $instance->title = $data['title'];
        $instance->type = $data['type'];

        return $instance;
    }

    public function jsonSerialize(): mixed
    {
        return (object)[
            'external_id' => $this->name,
            'name' => $this->title,
            'type' => $this->type->value,
        ];
    }
}
