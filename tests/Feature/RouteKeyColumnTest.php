<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RouteKeyColumnTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_dashboard_appointments_can_generate_nested_patient_and_doctor_routes(): void
    {
        $admin = User::factory()->admin()->create();
        $doctor = $this->doctor();
        $patient = Patient::factory()->create(['doctor_id' => $doctor->getKey()]);
        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->getKey(),
            'patient_id' => $patient->getKey(),
            'department_id' => $doctor->department_id,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('appointments.show', $appointment), false);
    }

    public function test_website_department_show_can_generate_doctor_profile_routes(): void
    {
        $doctor = $this->doctor();

        $this->get(route('website.departments.show', $doctor->department))
            ->assertOk()
            ->assertSee(route('website.doctors.show', $doctor), false);
    }

    public function test_relation_constraint_includes_custom_route_keys(): void
    {
        $this->assertSame(
            'doctor:id,doctor_code,name',
            Doctor::relationConstraint('doctor', ['name']),
        );

        $this->assertSame(
            'patient:id,patient_code,name',
            Patient::relationConstraint('patient', ['name']),
        );
    }

    private function doctor(): Doctor
    {
        $department = Department::factory()->create();

        return Doctor::factory()->create([
            'department_id' => $department->getKey(),
        ]);
    }
}
