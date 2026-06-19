<?php

namespace Tests\Unit\Models;

use App\Models\LabRequest;
use PHPUnit\Framework\TestCase;

class LabRequestTest extends TestCase
{
    public function test_is_completed_returns_true_for_completed_status(): void
    {
        $labRequest = new LabRequest;
        $labRequest->status = LabRequest::STATUS_COMPLETED;

        $this->assertTrue($labRequest->isCompleted());
    }

    public function test_is_completed_returns_false_for_pending(): void
    {
        $labRequest = new LabRequest;
        $labRequest->status = LabRequest::STATUS_PENDING;

        $this->assertFalse($labRequest->isCompleted());
    }

    public function test_is_completed_returns_false_for_cancelled(): void
    {
        $labRequest = new LabRequest;
        $labRequest->status = LabRequest::STATUS_CANCELLED;

        $this->assertFalse($labRequest->isCompleted());
    }
}
