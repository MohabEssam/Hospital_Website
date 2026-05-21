<?php

namespace App\Policies;

use App\Models\ScanResult;
use App\Models\User;

class ScanResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor() || $user->isScanCenter();
    }

    public function view(User $user, ScanResult $scanResult): bool
    {
        return $user->isAdmin()
            || ($user->isPatient() && $user->patientProfile()->value('id') === $scanResult->patient_id)
            || ($user->isDoctor() && $user->doctorProfile()->value('id') === $scanResult->doctor_id)
            || $user->isScanCenter();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isScanCenter();
    }

    public function update(User $user, ScanResult $scanResult): bool
    {
        return $user->isAdmin() || $user->isScanCenter();
    }

    public function delete(User $user, ScanResult $scanResult): bool
    {
        return $user->isAdmin();
    }
}
