<?php

namespace App\External\BallDontLie\DTOs;

readonly class TeamDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $city,
        public string $abbreviation,
        public string $conference,
        public string $division,
        public string $full_name,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['city'],
            $data['abbreviation'],
            $data['conference'],
            $data['division'],
            $data['full_name'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'abbreviation' => $this->abbreviation,
            'conference' => $this->conference,
            'division' => $this->division,
            'full_name' => $this->full_name,
        ];
    }
}
