<?php

namespace App\Models;

use App\Models\Concerns\HasRouteKeyColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Hospital extends Model
{
    use HasRouteKeyColumns;

    /** @var array<int, string> */
    public const ROUTE_COLUMNS = ['id', 'slug'];

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
