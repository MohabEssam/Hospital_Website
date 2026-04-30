<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Hospital extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'address',
        'image',
        'doctors',
    ];

    protected function casts(): array
    {
        return [
            'doctors' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Hospital $hospital): void {
            if (blank($hospital->slug)) {
                $hospital->slug = Str::slug($hospital->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
