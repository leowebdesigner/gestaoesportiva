<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Filterable
{
    public function applyFilters(Builder $query, array $filters): Builder
    {
        $ignored = ['page', 'per_page', 'sort', 'order', 'with', 'include'];
        $allowed = property_exists($this, 'allowedFilters') ? (array) $this->allowedFilters : [];

        foreach ($filters as $key => $value) {
            if (in_array($key, $ignored, true)) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            if ($allowed !== [] && !in_array($key, $allowed, true)) {
                continue;
            }

            $scopeMethod = 'scope' . Str::studly(Str::camel($key));

            if (method_exists($query->getModel(), $scopeMethod)) {
                $query->{Str::camel($key)}($value);
            }
        }

        return $query;
    }
}
