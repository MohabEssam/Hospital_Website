<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BookingSlotsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_patient_can_fetch_slots_by_doctor_numeric_id(): void
    {
        $doctor = $this->availableDoctor();
        $patientUser = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $patientUser->getKey()]);

        $response = $this->actingAs($patientUser)
            ->getJson(route('website.doctor.slots', ['doctor' => $doctor->getKey()]));

        $response->assertOk()
            ->assertJsonPath('doctor_id', $doctor->getKey())
            ->assertJsonPath('available', true);

        $slots = collect($response->json('days'))->flatMap(fn (array $day) => $day['slots']);
        $this->assertTrue($slots->contains(fn (array $slot) => $slot['available'] === true));
    }

    public function test_slots_endpoint_excludes_booked_times(): void
    {
        $doctor = $this->availableDoctor();
        $patientUser = User::factory()->patient()->create();
        $patient = Patient::factory()->create(['user_id' => $patientUser->getKey()]);

        $date = today()->addDay();
        $dayOfWeek = $date->dayOfWeek;

        DoctorSchedule::query()->create([
            'doctor_id' => $doctor->getKey(),
            'day_of_week' => $dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'is_available' => true,
        ]);

        Appointment::factory()->create([
            'doctor_id' => $doctor->getKey(),
            'patient_id' => $patient->getKey(),
            'department_id' => $doctor->department_id,
            'appointment_date' => $date->toDateString(),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'status' => Appointment::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($patientUser)
            ->getJson(route('website.doctor.slots', ['doctor' => $doctor->getKey()]));

        $response->assertOk();

        $day = collect($response->json('days'))->firstWhere('date', $date->toDateString());
        $this->assertNotNull($day);

        $nineAm = collect($day['slots'])->firstWhere('value', '09:00');
        $this->assertNotNull($nineAm);
        $this->assertFalse($nineAm['available']);
    }

    public function test_booking_page_loads_with_preselected_doctor_slots(): void
    {
        $doctor = $this->availableDoctor();
        $patientUser = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $patientUser->getKey()]);

        $this->actingAs($patientUser)
            ->get(route('website.book', ['doctor_id' => $doctor->getKey()]))
            ->assertOk()
            ->assertSee('Choose an Appointment Slot', false);
    }

    private function availableDoctor(): Doctor
    {
        $department = Department::factory()->create();

        $doctor = Doctor::factory()->create([
            'department_id' => $department->getKey(),
            'availability_status' => Doctor::STATUS_AVAILABLE,
        ]);

        DoctorSchedule::query()->create([
            'doctor_id' => $doctor->getKey(),
            'day_of_week' => today()->addDay()->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'is_available' => true,
        ]);

        return $doctor;
    }
}
