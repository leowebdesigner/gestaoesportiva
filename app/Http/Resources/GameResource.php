<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'home_team_id' => $this->home_team_id,
            'visitor_team_id' => $this->visitor_team_id,
            'home_team_score' => $this->home_team_score,
            'visitor_team_score' => $this->visitor_team_score,
            'season' => $this->season,
            'period' => $this->period,
            'status' => $this->status,
            'time' => $this->time,
            'postseason' => $this->postseason,
            'game_date' => $this->game_date?->toDateString(),
            'home_team' => new TeamResource($this->whenLoaded('homeTeam')),
            'visitor_team' => new TeamResource($this->whenLoaded('visitorTeam')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
