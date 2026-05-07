<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        $code = $this->appointment->id
            ? str_pad((string) $this->appointment->id, 6, '0', STR_PAD_LEFT)
            : 'PENDING';

        return new Envelope(
            subject: 'Your Medicare appointment has been confirmed (#' . $code . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointments.approved',
            with: [
                'appointment' => $this->appointment,
                'patient' => $this->appointment->patient,
                'doctor' => $this->appointment->doctor,
            ],
        );
    }
}
