<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ServesResultFiles;
use App\Models\Doctor;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\ScanRequest;
use App\Models\ScanResult;
use App\Services\QrCodeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientPortalController extends Controller
{
    use ServesResultFiles;

    public function dashboard(Request $request, QrCodeService $qrCode): View
    {
        $patient = $this->patientFor($request);

        return view('patient-portal.dashboard', [
            'patient' => $patient,
            'qrSvg' => $qrCode->svg($patient->patient_code, 5),
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

        return $this->serveResultFile($request, $labResult->file_paths, $file);
    }

    public function scanResultFile(Request $request, ScanResult $scanResult, int $file): StreamedResponse
    {
        Gate::authorize('view', $scanResult);

        return $this->serveResultFile($request, $scanResult->image_paths, $file);
    }

    private function patientFor(Request $request): Patient
    {
        $patient = $request->user()?->patientProfile()->first();

        abort_unless($patient, 403, 'A patient profile is required to view this portal.');

        $patient->loadMissing(Doctor::relationConstraint('doctor', ['name']));

        return $patient;
    }
}
