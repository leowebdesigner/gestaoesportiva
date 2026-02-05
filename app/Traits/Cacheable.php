<?php

namespace App\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    abstract protected function cachePrefix(): string;

    abstract protected function cacheTtl(): int;

    private int $cacheVersion = 0;

    protected function cacheRemember(string $key, Closure $callback): mixed
    {
        $version = $this->getCacheVersion();

        return Cache::remember(
            $this->cachePrefix() . "v{$version}:" . $key,
            $this->cacheTtl(),
            $callback
        );
    }

    protected function cacheForget(string $key): bool
    {
        $version = $this->getCacheVersion();

        return Cache::forget($this->cachePrefix() . "v{$version}:" . $key);
    }

    protected function cacheClearPrefix(): void
    {
        $versionKey = $this->cachePrefix() . '_version';
        $current = (int) Cache::get($versionKey, 0);
        Cache::forever($versionKey, $current + 1);
    }

    protected function cacheForgetItem(string $id): void
    {
        $this->cacheForget('id:' . $id);
    }

    private function getCacheVersion(): int
    {
        return (int) Cache::get($this->cachePrefix() . '_version', 0);
    }
}
