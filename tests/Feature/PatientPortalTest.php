<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientPortalTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_patient_dashboard_shows_only_the_authenticated_patient_records(): void
    {
        $doctor = $this->doctor();
        $user = User::factory()->patient()->create();
        $patient = Patient::factory()->create([
            'user_id' => $user->getKey(),
            'doctor_id' => $doctor->getKey(),
            'patient_code' => 'PAT-PORTAL',
        ]);
        $otherPatient = Patient::factory()->create([
            'doctor_id' => $doctor->getKey(),
            'patient_code' => 'PAT-OTHER',
        ]);

        Diagnosis::create([
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'title' => 'Seasonal allergy',
            'summary' => 'Mild respiratory allergy.',
            'diagnosed_at' => now(),
        ]);

        LabRequest::create([
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'test_name' => 'Complete Blood Count',
            'status' => LabRequest::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        Prescription::create([
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'medication_name' => 'Cetirizine',
            'status' => Prescription::STATUS_PENDING,
            'prescribed_at' => now(),
        ]);

        LabRequest::create([
            'patient_id' => $otherPatient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'test_name' => 'Other Patient Test',
            'status' => LabRequest::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('patient.dashboard'));

        $response
            ->assertOk()
            ->assertSee('PAT-PORTAL')
            ->assertSee('Seasonal allergy')
            ->assertSee('Complete Blood Count')
            ->assertSee('Cetirizine')
            ->assertDontSee('Other Patient Test');
    }

    public function test_patient_cannot_download_another_patient_lab_result_file(): void
    {
        Storage::fake('public');

        $doctor = $this->doctor();
        $owner = User::factory()->patient()->create();
        $ownerPatient = Patient::factory()->create([
            'user_id' => $owner->getKey(),
            'doctor_id' => $doctor->getKey(),
            'patient_code' => 'PAT-OWNER',
        ]);
        $intruder = User::factory()->patient()->create();
        Patient::factory()->create([
            'user_id' => $intruder->getKey(),
            'doctor_id' => $doctor->getKey(),
            'patient_code' => 'PAT-INTRUDER',
        ]);

        Storage::disk('public')->put('lab-results/owner.txt', 'private result');

        $labRequest = LabRequest::create([
            'patient_id' => $ownerPatient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'test_name' => 'Liver Function',
            'status' => LabRequest::STATUS_COMPLETED,
            'requested_at' => now(),
            'completed_at' => now(),
        ]);

        $labResult = LabResult::create([
            'lab_request_id' => $labRequest->getKey(),
            'patient_id' => $ownerPatient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'result_text' => 'Normal',
            'file_paths' => ['lab-results/owner.txt'],
            'resulted_at' => now(),
        ]);

        $this->actingAs($intruder)
            ->get(route('patient.lab-results.files', [$labResult, 0]))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('patient.lab-results.files', [$labResult, 0]))
            ->assertOk();
    }

    private function doctor(): Doctor
    {
        $department = Department::factory()->create();

        return Doctor::factory()->create([
            'department_id' => $department->getKey(),
        ]);
    }
}
