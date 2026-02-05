<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'city',
        'abbreviation',
        'conference',
        'division',
        'full_name',
    ];

    protected $casts = [
        'external_id' => 'integer',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeGames(): HasMany
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    public function awayGames(): HasMany
    {
        return $this->hasMany(Game::class, 'visitor_team_id');
    }

    /**
     * Get a query builder for all games (home and away) for this team.
     */
    public function allGamesQuery(): Builder
    {
        return Game::query()->byTeam($this->id);
    }

    public function scopeByConference(Builder $query, string $conference): Builder
    {
        return $query->where('conference', $conference);
    }

    public function scopeByDivision(Builder $query, string $division): Builder
    {
        return $query->where('division', $division);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('full_name', 'like', "%{$term}%");
        });
    }
}
