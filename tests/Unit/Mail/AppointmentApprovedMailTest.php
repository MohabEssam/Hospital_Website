<?php

namespace Tests\Unit\Mail;

use App\Mail\AppointmentApprovedMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use PHPUnit\Framework\TestCase;

class AppointmentApprovedMailTest extends TestCase
{
    public function test_envelope_contains_appointment_id_in_subject(): void
    {
        $appointment = new Appointment;
        $appointment->id = 42;

        $mail = new AppointmentApprovedMail($appointment);
        $envelope = $mail->envelope();

        $this->assertStringContainsString('000042', $envelope->subject);
        $this->assertStringContainsString('confirmed', $envelope->subject);
    }

    public function test_envelope_shows_pending_when_no_id(): void
    {
        $appointment = new Appointment;
        $appointment->id = null;

        $mail = new AppointmentApprovedMail($appointment);
        $envelope = $mail->envelope();

        $this->assertStringContainsString('PENDING', $envelope->subject);
    }

    public function test_envelope_pads_short_id(): void
    {
        $appointment = new Appointment;
        $appointment->id = 1;

        $mail = new AppointmentApprovedMail($appointment);
        $envelope = $mail->envelope();

        $this->assertStringContainsString('#000001', $envelope->subject);
    }

    public function test_content_uses_approved_view(): void
    {
        $appointment = new Appointment;
        $appointment->setRelation('patient', new Patient);
        $appointment->setRelation('doctor', new Doctor);

        $mail = new AppointmentApprovedMail($appointment);
        $content = $mail->content();

        $this->assertSame('emails.appointments.approved', $content->view);
    }

    public function test_mail_is_queueable(): void
    {
        $appointment = new Appointment;
        $mail = new AppointmentApprovedMail($appointment);

        $this->assertSame(3, $mail->tries);
        $this->assertSame(30, $mail->backoff);
    }

    public function test_appointment_is_accessible_on_mail(): void
    {
        $appointment = new Appointment;
        $appointment->id = 99;

        $mail = new AppointmentApprovedMail($appointment);

        $this->assertSame($appointment, $mail->appointment);
    }
}
