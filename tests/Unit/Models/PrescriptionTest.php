<?php

namespace Tests\Unit\Models;

use App\Models\Prescription;
use PHPUnit\Framework\TestCase;

class PrescriptionTest extends TestCase
{
    public function test_is_dispensed_returns_true_for_dispensed_status(): void
    {
        $prescription = new Prescription;
        $prescription->status = Prescription::STATUS_DISPENSED;

        $this->assertTrue($prescription->isDispensed());
    }

    public function test_is_dispensed_returns_false_for_pending(): void
    {
        $prescription = new Prescription;
        $prescription->status = Prescription::STATUS_PENDING;

        $this->assertFalse($prescription->isDispensed());
    }

    public function test_is_dispensed_returns_false_for_cancelled(): void
    {
        $prescription = new Prescription;
        $prescription->status = Prescription::STATUS_CANCELLED;

        $this->assertFalse($prescription->isDispensed());
    }
}
