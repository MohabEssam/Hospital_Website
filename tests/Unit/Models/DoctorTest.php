<?php

namespace Tests\Unit\Models;

use App\Models\Doctor;
use PHPUnit\Framework\TestCase;

class DoctorTest extends TestCase
{
    public function test_is_available_returns_true_for_available_status(): void
    {
        $doctor = new Doctor;
        $doctor->availability_status = Doctor::STATUS_AVAILABLE;

        $this->assertTrue($doctor->isAvailable());
    }

    public function test_is_available_returns_false_for_unavailable_status(): void
    {
        $doctor = new Doctor;
        $doctor->availability_status = Doctor::STATUS_UNAVAILABLE;

        $this->assertFalse($doctor->isAvailable());
    }

    public function test_is_available_returns_false_for_null_status(): void
    {
        $doctor = new Doctor;

        $this->assertFalse($doctor->isAvailable());
    }

    public function test_initials_returns_first_letters_without_dr_prefix(): void
    {
        $doctor = new Doctor;
        $doctor->name = 'Dr. Ahmed Hassan';

        $this->assertSame('AH', $doctor->initials());
    }

    public function test_initials_for_name_without_dr_prefix(): void
    {
        $doctor = new Doctor;
        $doctor->name = 'Ahmed Hassan';

        $this->assertSame('AH', $doctor->initials());
    }

    public function test_initials_single_name(): void
    {
        $doctor = new Doctor;
        $doctor->name = 'Dr. Ahmed';

        $this->assertSame('A', $doctor->initials());
    }

    public function test_availability_options(): void
    {
        $options = Doctor::availabilityOptions();

        $this->assertSame([Doctor::STATUS_AVAILABLE, Doctor::STATUS_UNAVAILABLE], $options);
    }

    public function test_route_key_name_is_doctor_code(): void
    {
        $doctor = new Doctor;

        $this->assertSame('doctor_code', $doctor->getRouteKeyName());
    }
}
