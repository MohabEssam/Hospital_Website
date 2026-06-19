<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Patient;
use App\Models\User;

trait AuthorizesPatientAccess
{
    protected function canViewPatient(Patient $patient, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->isAdmin()
            || $user->isReception()
            || (
                $user->isDoctor()
                && (
                    $patient->doctor_id === $user->doctorProfile?->getKey()
                    || $patient->appointments()
                        ->where('doctor_id', $user->doctorProfile?->getKey())
                        ->exists()
                )
            )
            || ($user->isPatient() && $patient->user_id === $user->getKey());
    }

    protected function canManagePatient(Patient $patient, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            $doctorId = $user->doctorProfile()->value('id');

            return $patient->doctor_id === $doctorId
                || $patient->appointments()
                    ->where('doctor_id', $doctorId)
                    ->exists();
        }

        return false;
    }
}
