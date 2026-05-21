<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispensePrescriptionRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PharmacyCenterController extends Controller
{
    public function index(Request $request): View
    {
        $patient = $this->findPatient($request);

        return view('pharmacy-center.index', [
            'patient' => $patient,
            'patientCode' => (string) $request->string('patient_code'),
            'prescriptions' => $patient
                ? $patient->prescriptions()
                    ->with([Doctor::relationConstraint('doctor', ['name']), 'diagnosis:id,title'])
                    ->orderByDesc('prescribed_at')
                    ->get()
                : collect(),
        ]);
    }

    public function update(DispensePrescriptionRequest $request, Prescription $prescription): RedirectResponse
    {
        $prescription->update([
            'status' => $request->validated('status'),
            'dispensed_at' => $request->validated('status') === Prescription::STATUS_DISPENSED
                ? $request->validated('dispensed_at')
                : null,
            'dispensed_by_id' => $request->validated('status') === Prescription::STATUS_DISPENSED
                ? $request->user()->getKey()
                : null,
        ]);

        return back()->with('status', 'Prescription status updated successfully.');
    }

    private function findPatient(Request $request): ?Patient
    {
        if (! $request->filled('patient_code')) {
            return null;
        }

        return Patient::query()
            ->where('patient_code', (string) $request->string('patient_code'))
            ->first();
    }
}
