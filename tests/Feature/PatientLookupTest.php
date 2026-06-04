<?php

namespace Tests\Feature;

use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PatientLookupTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_search_reception_lookup_by_phone_email_name_or_patient_id(): void
    {
        $admin = User::factory()->admin()->create();
        $patient = Patient::factory()->create([
            'doctor_id' => null,
            'patient_code' => 'PAT-00124',
            'name' => 'Ahmed Mohamed',
            'phone' => '01012345678',
            'email' => 'ahmed@example.test',
        ]);

        foreach (['PAT-00124', 'Ahmed', '010123', 'ahmed@example.test'] as $term) {
            $this->actingAs($admin)
                ->getJson(route('patients.lookup.search', ['context' => 'reception', 'q' => $term]))
                ->assertOk()
                ->assertJsonPath('results.0.patient_code', $patient->patient_code);
        }
    }

    public function test_lab_lookup_only_returns_patients_with_lab_requests(): void
    {
        $labStaff = User::factory()->labStaff()->create();
        $matchedPatient = Patient::factory()->create(['doctor_id' => null, 'name' => 'Ahmed Lab']);
        $hiddenPatient = Patient::factory()->create(['doctor_id' => null, 'name' => 'Ahmed No Orders']);

        LabRequest::create([
            'patient_id' => $matchedPatient->getKey(),
            'test_name' => 'CBC',
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($labStaff)
            ->getJson(route('patients.lookup.search', ['context' => 'lab', 'q' => 'Ahmed']));

        $response
            ->assertOk()
            ->assertJsonPath('results.0.patient_code', $matchedPatient->patient_code)
            ->assertJsonMissing(['patient_code' => $hiddenPatient->patient_code]);
    }

    public function test_lab_dashboard_can_find_patient_by_name(): void
    {
        $labStaff = User::factory()->labStaff()->create();
        $patient = Patient::factory()->create(['doctor_id' => null, 'name' => 'Ahmed Mohamed']);

        LabRequest::create([
            'patient_id' => $patient->getKey(),
            'test_name' => 'Complete Blood Count',
            'requested_at' => now(),
        ]);

        $this->actingAs($labStaff)
            ->get(route('lab.dashboard', ['patient_search' => 'Ahmed Mohamed']))
            ->assertOk()
            ->assertSee('Ahmed Mohamed')
            ->assertSee('Complete Blood Count');
    }

    public function test_pharmacy_lookup_can_find_patient_by_email_and_show_quantity(): void
    {
        $pharmacy = User::factory()->pharmacy()->create();
        $patient = Patient::factory()->create(['doctor_id' => null, 'email' => 'ahmed@example.test']);

        Prescription::create([
            'patient_id' => $patient->getKey(),
            'medication_name' => 'Ibuprofen',
            'quantity' => 12,
            'prescribed_at' => now(),
        ]);

        $this->actingAs($pharmacy)
            ->get(route('pharmacy.dashboard', ['patient_search' => 'ahmed@example.test']))
            ->assertOk()
            ->assertSee('Ibuprofen')
            ->assertSee('12');
    }

    public function test_module_lookup_rejects_unauthorized_contexts(): void
    {
        $pharmacy = User::factory()->pharmacy()->create();

        $this->actingAs($pharmacy)
            ->getJson(route('patients.lookup.search', ['context' => 'reception', 'q' => 'PAT']))
            ->assertForbidden();
    }

    public function test_patient_can_download_medical_card_pdf(): void
    {
        $user = User::factory()->patient()->create();
        Patient::factory()->create([
            'doctor_id' => null,
            'user_id' => $user->getKey(),
            'patient_code' => $user->public_id,
            'name' => $user->name,
        ]);

        $response = $this->actingAs($user)->get(route('patient.medical-card.download'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
    }
}
