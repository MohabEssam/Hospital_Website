<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DoctorScheduleService
{
    /**
     * @return Collection<int, array{date: Carbon, slots: Collection<int, array{time: string, label: string, available: bool, reason: string|null}>}>
     */
    public function weeklySlots(Doctor $doctor, ?Carbon $startDate = null, int $days = 6): Collection
    {
        $startDate ??= today();
        $doctor->loadMissing('schedules');

        $appointments = Appointment::query()
            ->whereBelongsTo($doctor)
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->whereBetween('appointment_date', [
                $startDate->copy()->toDateString(),
                $startDate->copy()->addDays($days - 1)->toDateString(),
            ])
            ->get(['appointment_date', 'start_time'])
            ->groupBy(fn (Appointment $appointment) => $appointment->appointment_date->toDateString());

        return collect(range(0, $days - 1))->map(function (int $offset) use ($doctor, $startDate, $appointments): array {
            $date = $startDate->copy()->addDays($offset);
            $bookedTimes = $appointments
                ->get($date->toDateString(), collect())
                ->pluck('start_time')
                ->map(fn (string $time) => Carbon::parse($time)->format('H:i'))
                ->all();

            $slots = $doctor->schedules
                ->where('day_of_week', $date->dayOfWeek)
                ->sortBy('start_time')
                ->values()
                ->map(function (DoctorSchedule $schedule) use ($bookedTimes): array {
                    $time = Carbon::parse($schedule->start_time)->format('H:i');
                    $isBooked = in_array($time, $bookedTimes, true);

                    return [
                        'time' => $time,
                        'label' => Carbon::parse($schedule->start_time)->format('g:i A'),
                        'available' => $schedule->is_available && ! $isBooked,
                        'reason' => ! $schedule->is_available ? 'Unavailable' : ($isBooked ? 'Booked' : null),
                    ];
                });

            return [
                'date' => $date,
                'slots' => $slots,
            ];
        });
    }

    public function slotIsAvailable(Doctor $doctor, string $date, string $startTime): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $normalizedTime = Carbon::createFromFormat('H:i', $startTime)->format('H:i:s');

        return $doctor->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', $normalizedTime)
            ->where('is_available', true)
            ->exists();
    }

    public function seedDefaultSchedule(Doctor $doctor): void
    {
        if ($doctor->schedules()->exists()) {
            return;
        }

        $slots = ['09:00', '10:30', '12:00', '14:00', '15:30'];

        foreach (range(1, 5) as $dayOfWeek) {
            foreach ($slots as $slot) {
                $doctor->schedules()->create([
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $slot,
                    'end_time' => Carbon::createFromFormat('H:i', $slot)->addMinutes(30)->format('H:i'),
                    'is_available' => true,
                ]);
            }
        }
    }
}
