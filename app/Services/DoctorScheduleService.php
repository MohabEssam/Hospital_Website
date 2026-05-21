<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DoctorScheduleService
{
    public const SLOT_DURATION_MINUTES = 30;

    /**
     * @return Collection<int, array{date: Carbon, slots: Collection<int, array{time: string, label: string, available: bool, reason: string|null}>}>
     */
    public function weeklySlots(Doctor $doctor, ?Carbon $startDate = null, int $days = 7): Collection
    {
        if (! $doctor->isAvailable()) {
            return collect();
        }

        $startDate ??= today();
        $doctor->loadMissing('schedules');

        if ($doctor->schedules->where('is_available', true)->isEmpty()) {
            $this->seedDefaultSchedule($doctor);
            $doctor->load('schedules');
        }

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
                ->map(fn (Appointment $appointment) => $this->normalizeTime((string) $appointment->start_time))
                ->all();

            $slots = $doctor->schedules
                ->where('day_of_week', $date->dayOfWeek)
                ->where('is_available', true)
                ->sortBy('start_time')
                ->flatMap(fn (DoctorSchedule $schedule) => $this->generateSlotsFromSchedule($schedule, $bookedTimes, $date))
                ->unique('time')
                ->sortBy('time')
                ->values();

            return [
                'date' => $date,
                'slots' => $slots,
            ];
        });
    }

    /**
     * @return array<int, array{time: string, label: string, available: bool, reason: string|null}>
     */
    public function generateSlotsFromSchedule(DoctorSchedule $schedule, array $bookedTimes, Carbon $date): array
    {
        $slots = [];
        $cursor = Carbon::parse($schedule->start_time);
        $windowEnd = Carbon::parse($schedule->end_time);

        while ($cursor->copy()->addMinutes(self::SLOT_DURATION_MINUTES)->lte($windowEnd)) {
            $time = $cursor->format('H:i');
            $isPast = $date->isToday() && $time <= now()->format('H:i');
            $isBooked = in_array($time, $bookedTimes, true);

            $slots[] = [
                'time' => $time,
                'label' => $cursor->format('g:i A'),
                'available' => ! $isPast && ! $isBooked,
                'reason' => $isPast ? 'Past' : ($isBooked ? 'Booked' : null),
            ];

            $cursor->addMinutes(self::SLOT_DURATION_MINUTES);
        }

        return $slots;
    }

    public function slotIsAvailable(Doctor $doctor, string $date, string $startTime): bool
    {
        if (! $doctor->isAvailable()) {
            return false;
        }

        $endTime = Carbon::createFromFormat('H:i', $startTime)
            ->addMinutes(self::SLOT_DURATION_MINUTES)
            ->format('H:i');

        return $this->rangeWithinSchedule($doctor, $date, $startTime, $endTime);
    }

    /**
     * Check if the requested appointment window fits entirely inside any
     * available weekly schedule block for that doctor on that weekday.
     */
    public function rangeWithinSchedule(Doctor $doctor, string $date, string $startTime, string $endTime): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedules = $doctor->schedules()
            ->select(['id', 'doctor_id', 'start_time', 'end_time'])
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->get();

        if ($schedules->isEmpty()) {
            return ! $doctor->schedules()->exists();
        }

        $requestedStart = Carbon::createFromFormat('H:i', $startTime);
        $requestedEnd = Carbon::createFromFormat('H:i', $endTime);

        foreach ($schedules as $schedule) {
            $windowStart = Carbon::parse($schedule->start_time);
            $windowEnd = Carbon::parse($schedule->end_time);

            if ($requestedStart->greaterThanOrEqualTo($windowStart)
                && $requestedEnd->lessThanOrEqualTo($windowEnd)) {
                return true;
            }
        }

        return false;
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
                    'end_time' => Carbon::createFromFormat('H:i', $slot)->addMinutes(self::SLOT_DURATION_MINUTES)->format('H:i'),
                    'is_available' => true,
                ]);
            }
        }
    }

    private function normalizeTime(string $time): string
    {
        return Carbon::parse($time)->format('H:i');
    }
}
