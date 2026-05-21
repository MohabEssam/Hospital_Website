<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['public_id', 'name', 'email', 'email_verified_at', 'password', 'role', 'remember_token'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_DOCTOR = 'doctor';

    public const ROLE_PATIENT = 'patient';

    public const ROLE_LAB = 'lab';

    public const ROLE_PHARMACY = 'pharmacy';

    public const ROLE_SCAN_CENTER = 'scan_center';

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (blank($user->public_id)) {
                $user->public_id = static::buildPublicId((string) $user->role);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
            self::ROLE_PATIENT,
            self::ROLE_PHARMACY,
            self::ROLE_LAB,
            self::ROLE_SCAN_CENTER,
        ];
    }

    public function doctorProfile(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * @param  array<int, string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isDoctor(): bool
    {
        return $this->hasRole(self::ROLE_DOCTOR);
    }

    public function isPatient(): bool
    {
        return $this->hasRole(self::ROLE_PATIENT);
    }

    public function isLab(): bool
    {
        return $this->hasRole(self::ROLE_LAB);
    }

    public function isScanCenter(): bool
    {
        return $this->hasRole(self::ROLE_SCAN_CENTER);
    }

    public function isPharmacy(): bool
    {
        return $this->hasRole(self::ROLE_PHARMACY);
    }

    public function isLabStaff(): bool
    {
        return $this->isLab();
    }

    public function isScanStaff(): bool
    {
        return $this->isScanCenter();
    }

    public static function prefixForRole(string $role): string
    {
        return match ($role) {
            self::ROLE_ADMIN => 'ADM',
            self::ROLE_DOCTOR => 'DR',
            self::ROLE_PATIENT => 'PAT',
            self::ROLE_LAB => 'LAB',
            self::ROLE_PHARMACY => 'PH',
            self::ROLE_SCAN_CENTER => 'SCAN',
            default => 'USR',
        };
    }

    protected static function buildPublicId(string $role): string
    {
        $prefix = static::prefixForRole($role);
        $next = static::query()
            ->where('public_id', 'like', "{$prefix}-%")
            ->pluck('public_id')
            ->map(function (string $publicId): int {
                preg_match('/-(\d+)$/', $publicId, $matches);

                return (int) ($matches[1] ?? 0);
            })
            ->max() + 1;

        do {
            $publicId = "{$prefix}-{$next}";
            $next++;
        } while (static::query()->where('public_id', $publicId)->exists());

        return $publicId;
    }
}
