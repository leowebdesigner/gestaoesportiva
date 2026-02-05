<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'team_id' => $this->team_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'position' => $this->position,
            'height' => $this->height,
            'weight' => $this->weight,
            'jersey_number' => $this->jersey_number,
            'college' => $this->college,
            'country' => $this->country,
            'draft_year' => $this->draft_year,
            'draft_round' => $this->draft_round,
            'draft_number' => $this->draft_number,
            'is_active' => $this->is_active,
            'team' => new TeamResource($this->whenLoaded('team')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
