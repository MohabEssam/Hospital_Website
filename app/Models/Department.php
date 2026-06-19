<?php

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Department extends Model
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'services',
        'icon',
        'hero_image',
        'sidebar_image',
        'contact_phone',
        'contact_email',
        'is_active',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'icon_url',
        'hero_image_url',
        'sidebar_image_url',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Department $department): void {
            $department->slug = static::buildUniqueSlug(
                $department->name,
                $department->getKey(),
            );
        });
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the icon image URL.
     */
    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? asset('storage/'.$this->icon) : null;
    }

    /**
     * Get the hero image URL.
     */
    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image ? asset('storage/'.$this->hero_image) : null;
    }

    /**
     * Get the sidebar image URL.
     */
    public function getSidebarImageUrlAttribute(): ?string
    {
        return $this->sidebar_image ? asset('storage/'.$this->sidebar_image) : null;
    }

    protected static function buildUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'department';
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
