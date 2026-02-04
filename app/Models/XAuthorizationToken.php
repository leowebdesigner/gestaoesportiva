<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XAuthorizationToken extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'name',
        'abilities',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
