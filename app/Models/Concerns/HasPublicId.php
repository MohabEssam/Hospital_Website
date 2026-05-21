<?php

namespace App\Models\Concerns;

trait HasPublicId
{
    use HasRouteKeyColumns;

    protected static function bootHasPublicId(): void
    {
        static::creating(function ($model): void {
            if (blank($model->public_id)) {
                $model->public_id = static::buildPublicId();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    protected static function buildPublicId(): string
    {
        $prefix = static::publicIdPrefix();
        $next = static::query()
            ->where('public_id', 'like', "{$prefix}-%")
            ->pluck('public_id')
            ->map(function (string $publicId): int {
                preg_match('/-(\d+)$/', $publicId, $matches);

                return (int) ($matches[1] ?? 0);
            })
            ->max() + 1;

        do {
            $publicId = "{$prefix}-{$next}";
            $next++;
        } while (static::query()->where('public_id', $publicId)->exists());

        return $publicId;
    }

    abstract protected static function publicIdPrefix(): string;
}
