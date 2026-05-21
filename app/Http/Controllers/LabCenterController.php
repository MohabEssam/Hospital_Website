<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabResultRequest;
use App\Models\Doctor;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabCenterController extends Controller
{
    public function index(Request $request): View
    {
        $patient = $this->findPatient($request);

        return view('lab-center.index', [
            'patient' => $patient,
            'patientCode' => (string) $request->string('patient_code'),
            'labRequests' => $patient
                ? $patient->labRequests()
                    ->with([
                        Doctor::relationConstraint('doctor', ['name']),
                        'diagnosis:id,title',
                        LabResult::relationConstraint('result', ['lab_request_id', 'result_text', 'file_paths', 'resulted_at', 'status']),
                    ])
                    ->orderByDesc('requested_at')
                    ->get()
                : collect(),
        ]);
    }

    public function storeResult(StoreLabResultRequest $request, LabRequest $labRequest): RedirectResponse
    {
        $paths = collect($request->file('files', []))
            ->map(fn ($file) => $file->store("lab-results/{$labRequest->getKey()}", 'public'))
            ->values()
            ->all();

        DB::transaction(function () use ($request, $labRequest, $paths): void {
            $result = LabResult::updateOrCreate(
                ['lab_request_id' => $labRequest->getKey()],
                [
                    'patient_id' => $labRequest->patient_id,
                    'doctor_id' => $labRequest->doctor_id,
                    'lab_id' => $labRequest->lab_id,
                    'entered_by_id' => $request->user()->getKey(),
                    'result_text' => $request->validated('result_text'),
                    'status' => $request->validated('status'),
                    'resulted_at' => $request->validated('resulted_at'),
                ],
            );

            if ($paths !== []) {
                $result->update([
                    'file_paths' => array_values(array_merge($result->file_paths ?? [], $paths)),
                ]);
            }

            $labRequest->update([
                'status' => LabRequest::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        });

        return back()->with('status', 'Lab result saved successfully.');
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
