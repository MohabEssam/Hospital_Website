<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait ScopesVisibleRecords
{
    protected function visibleAppointmentsQuery(User $user): Builder
    {
        $query = Appointment::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isDoctor() && $user->doctorProfile) {
            return $query->where('doctor_id', $user->doctorProfile->getKey());
        }

        if ($user->isPatient() && $user->patientProfile) {
            return $query->where('patient_id', $user->patientProfile->getKey());
        }

        return $query->whereRaw('1 = 0');
    }

    protected function visiblePatientsQuery(User $user): Builder
    {
        $query = Patient::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isDoctor() && $user->doctorProfile) {
            return $query->where('doctor_id', $user->doctorProfile->getKey());
        }

        if ($user->isPatient()) {
            return $query->where('user_id', $user->getKey());
        }

        return $query->whereRaw('1 = 0');
    }
}
