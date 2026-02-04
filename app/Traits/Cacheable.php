<?php

namespace App\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    protected function getCacheKey(string $key): string
    {
        return sprintf('%s:%s', static::class, $key);
    }

    protected function remember(string $key, $ttl, Closure $callback)
    {
        return Cache::remember($this->getCacheKey($key), $ttl, $callback);
    }

    protected function forget(string $key): bool
    {
        return Cache::forget($this->getCacheKey($key));
    }

    protected function flush(): bool
    {
        return Cache::flush();
    }
}
