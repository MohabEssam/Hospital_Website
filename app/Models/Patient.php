<?php

namespace App\Models;

use App\Models\Concerns\HasRouteKeyColumns;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, HasRouteKeyColumns;

    /** @var array<int, string> */
    public const ROUTE_COLUMNS = ['id', 'patient_code'];

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
        'age',
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
            'age' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Patient $patient): void {
            if (blank($patient->patient_code)) {
                $patient->patient_code = static::publicCodeForUser($patient) ?? static::buildPatientCode();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'patient_code';
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

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class);
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class);
    }

    public function scanRequests(): HasMany
    {
        return $this->hasMany(ScanRequest::class);
    }

    public function scanResults(): HasMany
    {
        return $this->hasMany(ScanResult::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function age(): ?int
    {
        return $this->date_of_birth?->age ?? $this->attributes['age'] ?? null;
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
            $code = 'PAT-'.$next++;
        } while (static::where('patient_code', $code)->exists());

        return $code;
    }

    protected static function publicCodeForUser(Patient $patient): ?string
    {
        $publicId = $patient->user_id
            ? User::query()->whereKey($patient->user_id)->value('public_id')
            : null;

        if (! is_string($publicId) || ! str_starts_with($publicId, 'PAT-')) {
            return null;
        }

        return static::query()->where('patient_code', $publicId)->exists()
            ? null
            : $publicId;
    }
}
