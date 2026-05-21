<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\ScanRequest;
use App\Models\ScanResult;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $patient = $this->patientFor($request);

        return view('patient-portal.dashboard', [
            'patient' => $patient,
            'diagnoses' => $patient->diagnoses()
                ->with(Doctor::relationConstraint('doctor', ['name', 'specialty']))
                ->orderByDesc('diagnosed_at')
                ->get(),
            'labRequests' => $patient->labRequests()
                ->with([
                    Doctor::relationConstraint('doctor', ['name', 'specialty']),
                    'diagnosis:id,title',
                    LabResult::relationConstraint('result', ['lab_request_id', 'result_text', 'file_paths', 'resulted_at', 'status']),
                ])
                ->orderByDesc('requested_at')
                ->get(),
            'scanRequests' => $patient->scanRequests()
                ->with([
                    Doctor::relationConstraint('doctor', ['name', 'specialty']),
                    'diagnosis:id,title',
                    ScanResult::relationConstraint('result', ['scan_request_id', 'findings', 'impression', 'image_paths', 'resulted_at', 'status']),
                ])
                ->orderByDesc('requested_at')
                ->get(),
            'prescriptions' => $patient->prescriptions()
                ->with([Doctor::relationConstraint('doctor', ['name', 'specialty']), 'diagnosis:id,title'])
                ->orderByDesc('prescribed_at')
                ->get(),
        ]);
    }

    public function results(Request $request): View
    {
        $patient = $this->patientFor($request);

        return view('patient-portal.results', [
            'patient' => $patient,
            'labResults' => $patient->labResults()
                ->with([
                    Doctor::relationConstraint('doctor', ['name', 'specialty']),
                    LabRequest::relationConstraint('labRequest', ['test_name', 'requested_at', 'status']),
                ])
                ->orderByDesc('resulted_at')
                ->get(),
            'scanResults' => $patient->scanResults()
                ->with([
                    Doctor::relationConstraint('doctor', ['name', 'specialty']),
                    ScanRequest::relationConstraint('scanRequest', ['scan_type', 'body_area', 'requested_at', 'status']),
                ])
                ->orderByDesc('resulted_at')
                ->get(),
            'prescriptions' => $patient->prescriptions()
                ->with(Doctor::relationConstraint('doctor', ['name', 'specialty']))
                ->orderByDesc('prescribed_at')
                ->get(),
        ]);
    }

    public function labResultFile(Request $request, LabResult $labResult, int $file): StreamedResponse
    {
        Gate::authorize('view', $labResult);

        $path = $labResult->file_paths[$file] ?? null;
        abort_unless(is_string($path) && Storage::disk('public')->exists($path), 404);

        return $request->boolean('download')
            ? Storage::disk('public')->download($path)
            : Storage::disk('public')->response($path);
    }

    public function scanResultFile(Request $request, ScanResult $scanResult, int $file): StreamedResponse
    {
        Gate::authorize('view', $scanResult);

        $path = $scanResult->image_paths[$file] ?? null;
        abort_unless(is_string($path) && Storage::disk('public')->exists($path), 404);

        return $request->boolean('download')
            ? Storage::disk('public')->download($path)
            : Storage::disk('public')->response($path);
    }

    private function patientFor(Request $request): Patient
    {
        $patient = $request->user()?->patientProfile()->first();

        abort_unless($patient, 403, 'A patient profile is required to view this portal.');

        $patient->loadMissing(Doctor::relationConstraint('doctor', ['name']));

        return $patient;
    }
}
