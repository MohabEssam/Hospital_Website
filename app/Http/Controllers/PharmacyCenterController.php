<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispensePrescriptionRequest;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Services\PatientLookupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PharmacyCenterController extends Controller
{
    public function index(Request $request, PatientLookupService $lookup): View
    {
        $patient = $lookup->find(
            $request->user(),
            PatientLookupService::CONTEXT_PHARMACY,
            (string) $request->string('patient_code'),
            (string) $request->string('patient_search'),
        );

        return view('pharmacy-center.index', [
            'patient' => $patient,
            'patientCode' => (string) $request->string('patient_code'),
            'patientSearch' => (string) $request->string('patient_search'),
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
}
