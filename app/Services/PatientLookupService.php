<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PatientLookupService
{
    public const CONTEXT_LAB = 'lab';

    public const CONTEXT_SCAN = 'scan';

    public const CONTEXT_PHARMACY = 'pharmacy';

    public const CONTEXT_RECEPTION = 'reception';

    /**
     * @return array<int, string>
     */
    public static function contexts(): array
    {
        return [
            self::CONTEXT_LAB,
            self::CONTEXT_SCAN,
            self::CONTEXT_PHARMACY,
            self::CONTEXT_RECEPTION,
        ];
    }

    public function authorize(User $user, string $context): bool
    {
        return match ($context) {
            self::CONTEXT_LAB => $user->isAdmin() || $user->isLab(),
            self::CONTEXT_SCAN => $user->isAdmin() || $user->isScanCenter(),
            self::CONTEXT_PHARMACY => $user->isAdmin() || $user->isPharmacy(),
            self::CONTEXT_RECEPTION => $user->isAdmin() || $user->isReception(),
            default => false,
        };
    }

    /**
     * @return Collection<int, Patient>
     */
    public function search(User $user, string $context, string $term, int $limit = 8): Collection
    {
        abort_unless($this->authorize($user, $context), 403);

        $term = $this->normalize($term);

        if (mb_strlen($term) < 2) {
            return collect();
        }

        return $this->query($user, $context)
            ->where(function (Builder $query) use ($term): void {
                $query
                    ->where('patient_code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->orderByRaw('case when patient_code = ? then 0 when patient_code like ? then 1 else 2 end', [$term, "{$term}%"])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function find(User $user, string $context, ?string $patientCode, ?string $search = null): ?Patient
    {
        abort_unless($this->authorize($user, $context), 403);

        $patientCode = $this->normalize((string) $patientCode);

        if ($patientCode !== '') {
            return $this->query($user, $context)
                ->where('patient_code', $patientCode)
                ->first();
        }

        $search = $this->normalize((string) $search);

        if ($search === '') {
            return null;
        }

        return $this->search($user, $context, $search, 1)->first();
    }

    public function normalize(string $term): string
    {
        return trim($term);
    }

    private function query(User $user, string $context): Builder
    {
        $query = Patient::query()
            ->select(['id', 'patient_code', 'name', 'phone', 'email', 'age', 'gender'])
            ->withExists([
                'labRequests',
                'scanRequests',
                'prescriptions',
            ]);

        return match ($context) {
            self::CONTEXT_LAB => $query->whereHas('labRequests'),
            self::CONTEXT_SCAN => $query->whereHas('scanRequests'),
            self::CONTEXT_PHARMACY => $query->whereHas('prescriptions'),
            self::CONTEXT_RECEPTION => $query,
            default => $query->whereRaw('1 = 0'),
        };
    }
}
