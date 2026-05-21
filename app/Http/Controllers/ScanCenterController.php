<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScanResultRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\ScanRequest;
use App\Models\ScanResult;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanCenterController extends Controller
{
    public function index(Request $request): View
    {
        $patient = $this->findPatient($request);

        return view('scan-center.index', [
            'patient' => $patient,
            'patientCode' => (string) $request->string('patient_code'),
            'scanRequests' => $patient
                ? $patient->scanRequests()
                    ->with([
                        Doctor::relationConstraint('doctor', ['name']),
                        'diagnosis:id,title',
                        ScanResult::relationConstraint('result', ['scan_request_id', 'findings', 'impression', 'image_paths', 'resulted_at', 'status']),
                    ])
                    ->orderByDesc('requested_at')
                    ->get()
                : collect(),
        ]);
    }

    public function storeResult(StoreScanResultRequest $request, ScanRequest $scanRequest): RedirectResponse
    {
        $paths = collect($request->file('images', []))
            ->map(fn ($file) => $file->store("scan-results/{$scanRequest->getKey()}", 'public'))
            ->values()
            ->all();

        DB::transaction(function () use ($request, $scanRequest, $paths): void {
            $result = ScanResult::updateOrCreate(
                ['scan_request_id' => $scanRequest->getKey()],
                [
                    'patient_id' => $scanRequest->patient_id,
                    'doctor_id' => $scanRequest->doctor_id,
                    'entered_by_id' => $request->user()->getKey(),
                    'findings' => $request->validated('findings'),
                    'impression' => $request->validated('impression'),
                    'status' => $request->validated('status'),
                    'resulted_at' => $request->validated('resulted_at'),
                ],
            );

            if ($paths !== []) {
                $result->update([
                    'image_paths' => array_values(array_merge($result->image_paths ?? [], $paths)),
                ]);
            }

            $scanRequest->update([
                'status' => ScanRequest::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        });

        return back()->with('status', 'Scan result saved successfully.');
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
