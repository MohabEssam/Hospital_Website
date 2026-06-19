<?php

namespace Tests\Unit\Notifications;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Notifications\AppointmentBookedNotification;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class AppointmentBookedNotificationTest extends TestCase
{
    public function test_via_returns_mail_channel(): void
    {
        $appointment = new Appointment;
        $notification = new AppointmentBookedNotification($appointment);

        $this->assertSame(['mail'], $notification->via(new \stdClass));
    }

    public function test_to_mail_returns_mail_message(): void
    {
        $doctor = new Doctor;
        $doctor->name = 'Dr. Ahmed Hassan';

        $patient = new Patient;
        $patient->name = 'Mohamed Ali';

        $appointment = new Appointment;
        $appointment->setRelation('doctor', $doctor);
        $appointment->setRelation('patient', $patient);
        $appointment->appointment_date = Carbon::parse('2026-07-15');
        $appointment->start_time = '09:30:00';

        $notification = new AppointmentBookedNotification($appointment);
        $mailMessage = $notification->toMail(new \stdClass);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
    }

    public function test_to_mail_contains_doctor_and_patient_names(): void
    {
        $doctor = new Doctor;
        $doctor->name = 'Dr. Ahmed Hassan';

        $patient = new Patient;
        $patient->name = 'Mohamed Ali';

        $appointment = new Appointment;
        $appointment->setRelation('doctor', $doctor);
        $appointment->setRelation('patient', $patient);
        $appointment->appointment_date = Carbon::parse('2026-07-15');
        $appointment->start_time = '09:30:00';

        $notification = new AppointmentBookedNotification($appointment);
        $mailMessage = $notification->toMail(new \stdClass);

        $data = $mailMessage->toArray();
        $introLines = implode(' ', $data['introLines']);

        $this->assertStringContainsString('Dr. Ahmed Hassan', $introLines);
        $this->assertStringContainsString('Mohamed Ali', $introLines);
    }

    public function test_to_mail_contains_appointment_date(): void
    {
        $doctor = new Doctor;
        $doctor->name = 'Dr. Test';

        $patient = new Patient;
        $patient->name = 'Patient Test';

        $appointment = new Appointment;
        $appointment->setRelation('doctor', $doctor);
        $appointment->setRelation('patient', $patient);
        $appointment->appointment_date = Carbon::parse('2026-12-25');
        $appointment->start_time = '14:00:00';

        $notification = new AppointmentBookedNotification($appointment);
        $mailMessage = $notification->toMail(new \stdClass);

        $data = $mailMessage->toArray();
        $introLines = implode(' ', $data['introLines']);

        $this->assertStringContainsString('Dec 25, 2026', $introLines);
        $this->assertStringContainsString('14:00', $introLines);
    }
}
