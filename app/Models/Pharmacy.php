<?php

namespace App\Models;

use App\Models\Concerns\HasRouteKeyColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pharmacy extends Model
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
