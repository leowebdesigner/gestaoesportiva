<?php

namespace App\External\BallDontLie\DTOs;

class PlayerDTO
{
    public function __construct(
        public int $id,
        public string $first_name,
        public string $last_name,
        public ?string $position,
        public ?string $height,
        public ?string $weight,
        public ?string $jersey_number,
        public ?string $college,
        public ?string $country,
        public ?int $draft_year,
        public ?int $draft_round,
        public ?int $draft_number,
        public ?array $team,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['position'] ?? null,
            $data['height'] ?? null,
            $data['weight'] ?? null,
            $data['jersey_number'] ?? null,
            $data['college'] ?? null,
            $data['country'] ?? null,
            $data['draft_year'] ?? null,
            $data['draft_round'] ?? null,
            $data['draft_number'] ?? null,
            $data['team'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'position' => $this->position,
            'height' => $this->height,
            'weight' => $this->weight,
            'jersey_number' => $this->jersey_number,
            'college' => $this->college,
            'country' => $this->country,
            'draft_year' => $this->draft_year,
            'draft_round' => $this->draft_round,
            'draft_number' => $this->draft_number,
            'team' => $this->team,
        ];
    }
}
