<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Notifications\AppointmentBookedNotification;
use Illuminate\Support\Facades\Notification;

trait SendsAppointmentNotifications
{
    protected function notifyAppointmentBooked(Appointment $appointment, Doctor $doctor): void
    {
        $appointment->load(['doctor', 'patient.user']);

        if ($appointment->patient?->user) {
            $appointment->patient->user->notify(new AppointmentBookedNotification($appointment));
        }

        if ($doctor->email) {
            Notification::route('mail', $doctor->email)
                ->notify(new AppointmentBookedNotification($appointment));
        }
    }
}
