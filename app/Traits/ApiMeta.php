<?php

namespace App\Traits;

trait ApiMeta
{
    /**
     * @return array{timestamp: string, version: string}
     */
    protected static function apiMeta(): array
    {
        return [
            'timestamp' => now()->toDateTimeString(),
            'version' => config('app.api_version', '1.0'),
        ];
    }
}
