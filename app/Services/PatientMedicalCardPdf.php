<?php

namespace App\Services;

use App\Models\Patient;

class PatientMedicalCardPdf
{
    public function __construct(private readonly QrCodeService $qrCode) {}

    public function make(Patient $patient): string
    {
        $content = $this->content($patient);
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 420 260] /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents 4 0 R >>',
            '<< /Length '.strlen($content).' >>'."\nstream\n".$content."\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($number + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT)." 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function content(Patient $patient): string
    {
        $commands = [
            '0.94 0.98 1 rg 0 0 420 260 re f',
            '1 1 1 rg 18 18 384 224 re f',
            '0.06 0.46 0.43 rg 18 206 384 36 re f',
            '1 1 1 rg 34 214 20 20 re f',
            '0.06 0.46 0.43 rg BT /F2 16 Tf 39 219 Td (M) Tj ET',
            '1 1 1 rg BT /F2 17 Tf 64 218 Td (Medicare Hospital) Tj ET',
            '0.10 0.13 0.20 rg BT /F2 18 Tf 34 174 Td (Digital Medical Card) Tj ET',
            '0.39 0.45 0.55 rg BT /F1 9 Tf 34 154 Td (Patient Name) Tj ET',
            '0.10 0.13 0.20 rg BT /F2 13 Tf 34 138 Td ('.$this->escape($patient->name).') Tj ET',
            '0.39 0.45 0.55 rg BT /F1 9 Tf 34 116 Td (Patient ID) Tj ET',
            '0.10 0.13 0.20 rg BT /F2 15 Tf 34 99 Td ('.$this->escape($patient->patient_code).') Tj ET',
            '0.39 0.45 0.55 rg BT /F1 9 Tf 34 77 Td (Phone Number) Tj ET',
            '0.10 0.13 0.20 rg BT /F1 12 Tf 34 61 Td ('.$this->escape($patient->phone ?: 'Not recorded').') Tj ET',
            '0.39 0.45 0.55 rg BT /F1 8 Tf 34 34 Td (Scan the QR code to open the patient record in authorized staff dashboards.) Tj ET',
        ];

        $matrix = $this->qrCode->matrix($patient->patient_code);
        $module = 4;
        $left = 276;
        $top = 176;

        $commands[] = '1 1 1 rg '.($left - 16).' '.($top - (count($matrix) * $module) - 16).' '.((count($matrix) * $module) + 32).' '.((count($matrix) * $module) + 32).' re f';
        $commands[] = '0.07 0.09 0.15 rg';

        foreach ($matrix as $rowIndex => $row) {
            foreach ($row as $columnIndex => $dark) {
                if (! $dark) {
                    continue;
                }

                $x = $left + ($columnIndex * $module);
                $y = $top - (($rowIndex + 1) * $module);
                $commands[] = "{$x} {$y} {$module} {$module} re f";
            }
        }

        return implode("\n", $commands);
    }

    private function escape(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }
}
