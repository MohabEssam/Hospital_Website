<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pharmacy extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'address',
        'image',
    ];

    protected static function booted(): void
    {
        static::saving(function (Pharmacy $pharmacy): void {
            if (blank($pharmacy->slug)) {
                $pharmacy->slug = Str::slug($pharmacy->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
