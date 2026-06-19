<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBookedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment->loadMissing(['doctor', 'patient']);

        return (new MailMessage)
            ->subject('Medicare appointment request received')
            ->greeting('Appointment request received')
            ->line('Your appointment request has been saved and is pending confirmation.')
            ->line('Doctor: '.($appointment->doctor?->name ?? 'Unassigned'))
            ->line('Patient: '.($appointment->patient?->name ?? 'Unknown'))
            ->line('Date: '.$appointment->appointment_date?->format('M d, Y'))
            ->line('Time: '.substr((string) $appointment->start_time, 0, 5))
            ->action('View bookings', route('my-bookings'))
            ->line('Thank you for choosing Medicare.');
    }
}
