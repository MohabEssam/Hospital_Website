<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use App\Services\PatientMedicalCardPdf;
use App\Services\QrCodeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PatientMedicalCardController extends Controller
{
    public function show(Request $request, QrCodeService $qrCode): View
    {
        $patient = $this->patientForUser($request->user());

        return $this->cardView($request, $patient, $qrCode);
    }

    public function download(Request $request, PatientMedicalCardPdf $pdf): Response
    {
        $patient = $this->patientForUser($request->user());

        return $this->pdfResponse($patient, $pdf);
    }

    public function showForStaff(Request $request, Patient $patient, QrCodeService $qrCode): View
    {
        $this->authorizeStaffAccess($request->user(), $patient);

        return $this->cardView($request, $patient, $qrCode);
    }

    public function downloadForStaff(Request $request, Patient $patient, PatientMedicalCardPdf $pdf): Response
    {
        $this->authorizeStaffAccess($request->user(), $patient);

        return $this->pdfResponse($patient, $pdf);
    }

    private function cardView(Request $request, Patient $patient, QrCodeService $qrCode): View
    {
        return view('patients.medical-card', [
            'layout' => $request->user()->isPatient() ? 'layouts.website' : 'layouts.app',
            'patient' => $patient,
            'qrSvg' => $qrCode->svg($patient->patient_code),
            'downloadRoute' => match (true) {
                $request->user()->isPatient() => route('patient.medical-card.download'),
                $request->user()->isReception() => route('reception.patients.medical-card.download', $patient),
                default => route('patients.medical-card.download', $patient),
            },
        ]);
    }

    private function pdfResponse(Patient $patient, PatientMedicalCardPdf $pdf): Response
    {
        return response($pdf->make($patient), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$patient->patient_code.'-medical-card.pdf"',
        ]);
    }

    private function patientForUser(User $user): Patient
    {
        $patient = $user->patientProfile()->first();

        abort_unless($patient, 403, 'A patient profile is required to view this card.');

        return $patient;
    }

    private function authorizeStaffAccess(User $user, Patient $patient): void
    {
        abort_unless(
            $user->isAdmin()
            || $user->isReception()
            || (
                $user->isDoctor()
                && (
                    $patient->doctor_id === $user->doctorProfile?->getKey()
                    || $patient->appointments()->where('doctor_id', $user->doctorProfile?->getKey())->exists()
                )
            ),
            403,
        );
    }
}
