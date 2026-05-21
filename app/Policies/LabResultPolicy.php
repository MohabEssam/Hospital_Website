<?php

namespace App\Policies;

use App\Models\LabResult;
use App\Models\User;

class LabResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor() || $user->isLab();
    }

    public function view(User $user, LabResult $labResult): bool
    {
        return $user->isAdmin()
            || ($user->isPatient() && $user->patientProfile()->value('id') === $labResult->patient_id)
            || ($user->isDoctor() && $user->doctorProfile()->value('id') === $labResult->doctor_id)
            || $user->isLab();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isLab();
    }

    public function update(User $user, LabResult $labResult): bool
    {
        return $user->isAdmin() || $user->isLab();
    }

    public function delete(User $user, LabResult $labResult): bool
    {
        return $user->isAdmin();
    }
}
