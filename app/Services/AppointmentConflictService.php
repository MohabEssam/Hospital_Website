<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;

class AppointmentConflictService
{
    public function hasConflict(
        int $doctorId,
        string $appointmentDate,
        string $startTime,
        string $endTime,
        ?Appointment $ignore = null,
    ): bool {
        return $this->query($doctorId, $appointmentDate, $startTime, $endTime, $ignore)->exists();
    }

    public function query(
        int $doctorId,
        string $appointmentDate,
        string $startTime,
        string $endTime,
        ?Appointment $ignore = null,
    ): Builder {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $appointmentDate)
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->when($ignore, fn (Builder $query) => $query->whereKeyNot($ignore->getKey()))
            ->where(function (Builder $query) use ($startTime, $endTime): void {
                $query
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });
    }
}
