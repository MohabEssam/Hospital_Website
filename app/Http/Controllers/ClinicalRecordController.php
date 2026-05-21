<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClinicalRecordRequest;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\ScanRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ClinicalRecordController extends Controller
{
    public function create(Patient $patient): View
    {
        abort_unless($this->canManagePatient($patient, auth()->user()), 403);

        $patient->load([
            Doctor::relationConstraint('doctor', ['name']),
            'appointments' => fn ($query) => $query
                ->select(['id', 'patient_id', 'doctor_id', 'appointment_date', 'start_time', 'status', 'treatment'])
                ->orderByDesc('appointment_date'),
        ]);

        return view('clinical-records.create', [
            'patient' => $patient,
        ]);
    }

    public function store(StoreClinicalRecordRequest $request, Patient $patient): RedirectResponse
    {
        abort_unless($this->canManagePatient($patient, $request->user()), 403);

        $validated = $request->validated();
        $doctorId = $request->user()->isDoctor()
            ? $request->user()->doctorProfile()->value('id')
            : $patient->doctor_id;

        if (isset($validated['appointment_id'])) {
            abort_unless(
                $patient->appointments()->whereKey($validated['appointment_id'])->exists(),
                403,
            );
        }

        DB::transaction(function () use ($patient, $doctorId, $validated): void {
            $diagnosis = Diagnosis::create([
                'patient_id' => $patient->getKey(),
                'doctor_id' => $doctorId,
                'appointment_id' => $validated['appointment_id'] ?? null,
                'title' => $validated['title'],
                'summary' => $validated['summary'] ?? null,
                'symptoms' => $validated['symptoms'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
                'diagnosed_at' => $validated['diagnosed_at'],
            ]);

            foreach ($this->filledRows($validated['lab_requests'] ?? [], 'test_name') as $row) {
                LabRequest::create([
                    'patient_id' => $patient->getKey(),
                    'doctor_id' => $doctorId,
                    'diagnosis_id' => $diagnosis->getKey(),
                    'test_name' => $row['test_name'],
                    'specimen' => $row['specimen'] ?? null,
                    'priority' => $row['priority'] ?? 'routine',
                    'instructions' => $row['instructions'] ?? null,
                    'status' => LabRequest::STATUS_PENDING,
                    'requested_at' => now(),
                ]);
            }

            foreach ($this->filledRows($validated['scan_requests'] ?? [], 'scan_type') as $row) {
                ScanRequest::create([
                    'patient_id' => $patient->getKey(),
                    'doctor_id' => $doctorId,
                    'diagnosis_id' => $diagnosis->getKey(),
                    'scan_type' => $row['scan_type'],
                    'body_area' => $row['body_area'] ?? null,
                    'contrast_required' => (bool) ($row['contrast_required'] ?? false),
                    'instructions' => $row['instructions'] ?? null,
                    'status' => ScanRequest::STATUS_PENDING,
                    'requested_at' => now(),
                ]);
            }

            foreach ($this->filledRows($validated['prescriptions'] ?? [], 'medication_name') as $row) {
                Prescription::create([
                    'patient_id' => $patient->getKey(),
                    'doctor_id' => $doctorId,
                    'diagnosis_id' => $diagnosis->getKey(),
                    'medication_name' => $row['medication_name'],
                    'dosage' => $row['dosage'] ?? null,
                    'frequency' => $row['frequency'] ?? null,
                    'duration' => $row['duration'] ?? null,
                    'quantity' => $row['quantity'] ?? null,
                    'instructions' => $row['instructions'] ?? null,
                    'status' => Prescription::STATUS_PENDING,
                    'prescribed_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Clinical record created successfully.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function filledRows(array $rows, string $requiredKey): array
    {
        return collect($rows)
            ->filter(fn (array $row) => filled($row[$requiredKey] ?? null))
            ->values()
            ->all();
    }

    private function canManagePatient(Patient $patient, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            $doctorId = $user->doctorProfile()->value('id');

            return $patient->doctor_id === $doctorId
                || $patient->appointments()
                    ->where('doctor_id', $doctorId)
                    ->exists();
        }

        return false;
    }
}
