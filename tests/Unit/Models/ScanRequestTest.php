<?php

namespace Tests\Unit\Models;

use App\Models\ScanRequest;
use PHPUnit\Framework\TestCase;

class ScanRequestTest extends TestCase
{
    public function test_is_completed_returns_true_for_completed_status(): void
    {
        $scanRequest = new ScanRequest;
        $scanRequest->status = ScanRequest::STATUS_COMPLETED;

        $this->assertTrue($scanRequest->isCompleted());
    }

    public function test_is_completed_returns_false_for_pending(): void
    {
        $scanRequest = new ScanRequest;
        $scanRequest->status = ScanRequest::STATUS_PENDING;

        $this->assertFalse($scanRequest->isCompleted());
    }

    public function test_is_completed_returns_false_for_cancelled(): void
    {
        $scanRequest = new ScanRequest;
        $scanRequest->status = ScanRequest::STATUS_CANCELLED;

        $this->assertFalse($scanRequest->isCompleted());
    }
}
