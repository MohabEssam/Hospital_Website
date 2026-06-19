<?php

namespace Tests\Unit\Services;

use App\Models\Patient;
use App\Services\PatientMedicalCardPdf;
use App\Services\QrCodeService;
use PHPUnit\Framework\TestCase;

class PatientMedicalCardPdfTest extends TestCase
{
    public function test_make_returns_valid_pdf(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Ahmed Mohamed';
        $patient->patient_code = 'PAT-001';
        $patient->phone = '01012345678';

        $pdf = $service->make($patient);

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringEndsWith('%%EOF', $pdf);
    }

    public function test_pdf_contains_patient_name(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Ahmed Mohamed';
        $patient->patient_code = 'PAT-001';
        $patient->phone = '01012345678';

        $pdf = $service->make($patient);

        $this->assertStringContainsString('Ahmed Mohamed', $pdf);
    }

    public function test_pdf_contains_patient_code(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Test';
        $patient->patient_code = 'PAT-999';
        $patient->phone = '01000000000';

        $pdf = $service->make($patient);

        $this->assertStringContainsString('PAT-999', $pdf);
    }

    public function test_pdf_contains_phone_number(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Test';
        $patient->patient_code = 'PAT-001';
        $patient->phone = '01098765432';

        $pdf = $service->make($patient);

        $this->assertStringContainsString('01098765432', $pdf);
    }

    public function test_pdf_shows_not_recorded_when_phone_is_null(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Test';
        $patient->patient_code = 'PAT-001';
        $patient->phone = null;

        $pdf = $service->make($patient);

        $this->assertStringContainsString('Not recorded', $pdf);
    }

    public function test_pdf_escapes_special_characters_in_name(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Name (with) parentheses';
        $patient->patient_code = 'PAT-001';
        $patient->phone = '01000000000';

        $pdf = $service->make($patient);

        $this->assertStringContainsString('Name \\(with\\) parentheses', $pdf);
    }

    public function test_pdf_escapes_backslashes_in_name(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Name\\Slash';
        $patient->patient_code = 'PAT-001';
        $patient->phone = '01000000000';

        $pdf = $service->make($patient);

        $this->assertStringContainsString('Name\\\\Slash', $pdf);
    }

    public function test_pdf_contains_hospital_name(): void
    {
        $service = new PatientMedicalCardPdf(new QrCodeService);

        $patient = new Patient;
        $patient->name = 'Test';
        $patient->patient_code = 'PAT-001';
        $patient->phone = '01000000000';

        $pdf = $service->make($patient);

        $this->assertStringContainsString('Medicare Hospital', $pdf);
    }
}
