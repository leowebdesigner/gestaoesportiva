<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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

    public function allGames()
    {
        return Game::query()->where(function ($query) {
            $query->where('home_team_id', $this->id)
                ->orWhere('visitor_team_id', $this->id);
        });
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
