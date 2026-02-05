<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Game extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'home_team_id',
        'visitor_team_id',
        'home_team_score',
        'visitor_team_score',
        'season',
        'period',
        'status',
        'time',
        'postseason',
        'game_date',
    ];

    protected $casts = [
        'external_id' => 'integer',
        'home_team_score' => 'integer',
        'visitor_team_score' => 'integer',
        'season' => 'integer',
        'period' => 'integer',
        'postseason' => 'boolean',
        'game_date' => 'date',
    ];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function visitorTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'visitor_team_id');
    }

    public function scopeBySeason(Builder $query, int $season): Builder
    {
        return $query->where('season', $season);
    }

    public function scopeByTeam(Builder $query, string $teamId): Builder
    {
        return $query->where(function ($q) use ($teamId) {
            $q->where('home_team_id', $teamId)
                ->orWhere('visitor_team_id', $teamId);
        });
    }

    public function scopePlayoffs(Builder $query): Builder
    {
        return $query->where('postseason', true);
    }

    public function scopeRegularSeason(Builder $query): Builder
    {
        return $query->where('postseason', false);
    }

    public function scopePostseason(Builder $query, bool|string|int $postseason): Builder
    {
        return $query->where('postseason', filter_var($postseason, FILTER_VALIDATE_BOOLEAN));
    }

    public function scopeByDateRange(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('game_date', [$start, $end]);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
