<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasRouteKeyColumns
{
    /**
     * Minimum columns required for route model binding and URL generation.
     *
     * @return array<int, string>
     */
    public static function routeKeyColumns(): array
    {
        $instance = new static;
        $routeKey = $instance->getRouteKeyName();

        return $routeKey === 'id'
            ? ['id']
            : ['id', $routeKey];
    }

    /**
     * Merge route-key columns with additional columns (deduplicated, id first).
     *
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    public static function columnsFor(array $columns): array
    {
        return array_values(array_unique([
            ...static::routeKeyColumns(),
            ...$columns,
        ]));
    }

    /**
     * Eager-load constraint string, e.g. "doctor:id,doctor_code,name".
     *
     * @param  array<int, string>  $columns
     */
    public static function relationConstraint(string $relation, array $columns): string
    {
        return $relation.':'.implode(',', static::columnsFor($columns));
    }

    /**
     * @param  Builder<static>  $query
     * @param  array<int, string>  $columns
     * @return Builder<static>
     */
    public function scopeSelectColumns(Builder $query, array $columns): Builder
    {
        return $query->select(static::columnsFor($columns));
    }
}
