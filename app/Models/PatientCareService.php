<?php

namespace App\Models;

use App\Models\Concerns\HasRouteKeyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PatientCareService extends Model
{
    use HasFactory, HasRouteKeyColumns;

    /** @var array<int, string> */
    public const ROUTE_COLUMNS = ['id', 'slug'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'image',
        'icon_class',
        'is_bookable',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_bookable' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (PatientCareService $service): void {
            $service->slug = static::buildUniqueSlug(
                $service->name,
                $service->getKey(),
            );
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBookable($query)
    {
        return $query->where('is_bookable', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function buildUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'service';
        $slug = $base;
        $suffix = 1;

        while (
            static::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
