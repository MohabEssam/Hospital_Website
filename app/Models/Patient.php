<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_NEW = 'new_patient';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'user_id',
        'doctor_id',
        'name',
        'patient_code',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'check_in_date',
        'treatment',
        'room_number',
        'status',
        'address',
        'notes',
        'avatar_path',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'check_in_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Patient $patient): void {
            if (blank($patient->patient_code)) {
                $patient->patient_code = static::buildPatientCode();
            }
        });
    }

    /**
     * @return array<int, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_NEW,
            self::STATUS_INACTIVE,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function age(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }

    public function ageGroup(): string
    {
        $age = $this->age();

        if ($age === null) {
            return 'unknown';
        }

        if ($age <= 17) {
            return 'child';
        }

        if ($age >= 60) {
            return 'elderly';
        }

        return 'adult';
    }

    protected static function buildPatientCode(): string
    {
        $next = (static::max('id') ?? 0) + 1;

        do {
            $code = 'PAT-'.str_pad((string) $next++, 4, '0', STR_PAD_LEFT);
        } while (static::where('patient_code', $code)->exists());

        return $code;
    }
}
