<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Filterable
{
    public function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $scopeMethod = 'scope' . Str::studly(Str::camel($key));

            if (method_exists($query->getModel(), $scopeMethod)) {
                $query->{Str::camel($key)}($value);
                continue;
            }

            $query->where($key, $value);
        }

        return $query;
    }
}
