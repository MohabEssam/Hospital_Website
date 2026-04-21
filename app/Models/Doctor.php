<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_UNAVAILABLE = 'unavailable';

    protected $fillable = [
        'user_id',
        'department_id',
        'name',
        'slug',
        'doctor_code',
        'email',
        'phone',
        'specialty',
        'biography',
        'address',
        'availability_status',
        'consultation_fee',
        'avatar_path',
        'years_of_experience',
    ];

    protected function casts(): array
    {
        return [
            'consultation_fee' => 'decimal:2',
            'years_of_experience' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Doctor $doctor): void {
            $doctor->slug = static::buildUniqueSlug(
                $doctor->name,
                $doctor->getKey(),
            );

            if (blank($doctor->doctor_code)) {
                $doctor->doctor_code = static::buildDoctorCode($doctor->getKey());
            }
        });
    }

    /**
     * @return array<int, string>
     */
    public static function availabilityOptions(): array
    {
        return [
            self::STATUS_AVAILABLE,
            self::STATUS_UNAVAILABLE,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function isAvailable(): bool
    {
        return $this->availability_status === self::STATUS_AVAILABLE;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->replace('Dr. ', '')
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }

    public function todayAppointmentsCount(?Carbon $day = null): int
    {
        $day ??= today();

        return $this->appointments()
            ->whereDate('appointment_date', $day)
            ->count();
    }

    protected static function buildUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'doctor';
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

    protected static function buildDoctorCode(?int $ignoreId = null): string
    {
        $next = (static::max('id') ?? 0) + 1;

        do {
            $code = 'DOC-'.str_pad((string) $next++, 4, '0', STR_PAD_LEFT);
        } while (
            static::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('doctor_code', $code)
                ->exists()
        );

        return $code;
    }
}
