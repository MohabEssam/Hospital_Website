<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class AppointmentConflictService
{
    public function hasConflict(
        int $doctorId,
        string $appointmentDate,
        string $startTime,
        string $endTime,
        ?Appointment $ignore = null,
    ): bool {
        $query = $this->query($doctorId, $appointmentDate, $startTime, $endTime, $ignore);
        $conflicts = (clone $query)
            ->get(['id', 'status', 'appointment_date', 'start_time', 'end_time']);

        Log::debug('Appointment slot conflict check completed.', [
            'doctor_id' => $doctorId,
            'appointment_date' => $appointmentDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'ignore_appointment_id' => $ignore?->getKey(),
            'slot_blocking_statuses' => Appointment::slotBlockingStatuses(),
            'conflict_count' => $conflicts->count(),
            'conflicts' => $conflicts->map(fn (Appointment $appointment): array => [
                'id' => $appointment->getKey(),
                'status' => $appointment->status,
                'appointment_date' => $appointment->appointment_date?->toDateString(),
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
            ])->all(),
        ]);

        return $conflicts->isNotEmpty();
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
            ->whereIn('status', Appointment::slotBlockingStatuses())
            ->when($ignore, fn (Builder $query) => $query->whereKeyNot($ignore->getKey()))
            ->where(function (Builder $query) use ($startTime, $endTime): void {
                $query
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });
    }
}
