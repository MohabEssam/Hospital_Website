<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\ScanRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClinicalWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_doctor_can_create_diagnosis_with_orders_for_assigned_patient(): void
    {
        $doctor = $this->doctor();
        $doctorUser = $doctor->user()->firstOrFail();
        $patient = Patient::factory()->create([
            'doctor_id' => $doctor->getKey(),
            'patient_code' => 'PAT-WORKFLOW',
        ]);

        $response = $this->actingAs($doctorUser)->post(route('patients.clinical-records.store', $patient), [
            'title' => 'Acute bronchitis',
            'summary' => 'Cough and mild fever.',
            'status' => 'active',
            'diagnosed_at' => now()->toDateTimeString(),
            'lab_requests' => [
                ['test_name' => 'Complete Blood Count', 'priority' => 'routine'],
            ],
            'scan_requests' => [
                ['scan_type' => 'Chest X-ray', 'body_area' => 'Chest', 'contrast_required' => 0],
            ],
            'prescriptions' => [
                ['medication_name' => 'Azithromycin', 'dosage' => '500mg', 'frequency' => 'Once daily'],
            ],
        ]);

        $response->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('diagnoses', [
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'title' => 'Acute bronchitis',
        ]);
        $this->assertDatabaseHas('lab_requests', ['patient_id' => $patient->getKey(), 'test_name' => 'Complete Blood Count']);
        $this->assertDatabaseHas('scan_requests', ['patient_id' => $patient->getKey(), 'scan_type' => 'Chest X-ray']);
        $this->assertDatabaseHas('prescriptions', ['patient_id' => $patient->getKey(), 'medication_name' => 'Azithromycin']);
    }

    public function test_lab_role_can_search_by_patient_public_id_and_save_result(): void
    {
        Storage::fake('public');

        $doctor = $this->doctor();
        $patient = Patient::factory()->create([
            'doctor_id' => $doctor->getKey(),
            'patient_code' => 'PAT-1',
        ]);
        $labRequest = LabRequest::create([
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'test_name' => 'CBC',
            'requested_at' => now(),
        ]);
        $staff = User::factory()->labStaff()->create();

        $this->actingAs($staff)
            ->get(route('lab.dashboard', ['patient_code' => 'PAT-1']))
            ->assertOk()
            ->assertSee('CBC');

        $this->actingAs($staff)->post(route('lab.results.store', $labRequest), [
            'result_text' => 'Within normal range.',
            'status' => 'final',
            'files' => [UploadedFile::fake()->create('cbc.pdf', 100, 'application/pdf')],
        ])->assertRedirect();

        $this->assertDatabaseHas('lab_results', [
            'lab_request_id' => $labRequest->getKey(),
            'patient_id' => $patient->getKey(),
            'result_text' => 'Within normal range.',
        ]);
        $this->assertSame(LabRequest::STATUS_COMPLETED, $labRequest->fresh()->status);
    }

    public function test_scan_center_can_save_scan_result_and_pharmacy_can_dispense(): void
    {
        Storage::fake('public');

        $doctor = $this->doctor();
        $patient = Patient::factory()->create([
            'doctor_id' => $doctor->getKey(),
            'patient_code' => 'PAT-SCAN',
        ]);
        $scanRequest = ScanRequest::create([
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'scan_type' => 'MRI',
            'requested_at' => now(),
        ]);
        $prescription = Prescription::create([
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'medication_name' => 'Ibuprofen',
            'prescribed_at' => now(),
        ]);

        $scanStaff = User::factory()->scanStaff()->create();
        $this->actingAs($scanStaff)->post(route('scan-center.results.store', $scanRequest), [
            'impression' => 'No acute findings.',
            'status' => 'final',
            'images' => [UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf')],
        ])->assertRedirect();

        $pharmacy = User::factory()->pharmacy()->create();
        $this->actingAs($pharmacy)->patch(route('pharmacy-center.prescriptions.update', $prescription), [
            'status' => Prescription::STATUS_DISPENSED,
        ])->assertRedirect();

        $this->assertSame(ScanRequest::STATUS_COMPLETED, $scanRequest->fresh()->status);
        $this->assertSame(Prescription::STATUS_DISPENSED, $prescription->fresh()->status);
    }

    public function test_admin_can_create_lab_user_with_public_id(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('staff-users.store'), [
            'name' => 'Main Lab',
            'email' => 'lab@example.test',
            'phone' => '01099998888',
            'gender' => User::GENDER_FEMALE,
            'role' => User::ROLE_LAB,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('staff-users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'lab@example.test',
            'phone' => '01099998888',
            'gender' => User::GENDER_FEMALE,
            'role' => User::ROLE_LAB,
            'public_id' => 'LAB-1',
        ]);
    }

    private function doctor(): Doctor
    {
        $department = Department::factory()->create();

        return Doctor::factory()->create([
            'department_id' => $department->getKey(),
        ]);
    }
}
