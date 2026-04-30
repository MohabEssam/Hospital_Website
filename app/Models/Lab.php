<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lab extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'address',
        'work_hours',
        'image',
        'xrays',
        'medical_tests',
    ];

    protected function casts(): array
    {
        return [
            'xrays' => 'array',
            'medical_tests' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Lab $lab): void {
            if (blank($lab->slug)) {
                $lab->slug = Str::slug($lab->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
