<?php

namespace App\External\BallDontLie\DTOs;

class GameDTO
{
    public function __construct(
        public int $id,
        public array $home_team,
        public array $visitor_team,
        public int $home_team_score,
        public int $visitor_team_score,
        public int $season,
        public int $period,
        public string $status,
        public ?string $time,
        public bool $postseason,
        public string $date,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['home_team'],
            $data['visitor_team'],
            $data['home_team_score'] ?? 0,
            $data['visitor_team_score'] ?? 0,
            $data['season'],
            $data['period'] ?? 0,
            $data['status'] ?? '',
            $data['time'] ?? null,
            $data['postseason'] ?? false,
            $data['date'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'home_team' => $this->home_team,
            'visitor_team' => $this->visitor_team,
            'home_team_score' => $this->home_team_score,
            'visitor_team_score' => $this->visitor_team_score,
            'season' => $this->season,
            'period' => $this->period,
            'status' => $this->status,
            'time' => $this->time,
            'postseason' => $this->postseason,
            'date' => $this->date,
        ];
    }
}
