<?php

namespace Tests\Unit\Models;

use App\Models\ServiceBooking;
use PHPUnit\Framework\TestCase;

class ServiceBookingTest extends TestCase
{
    public function test_status_options_contains_expected_statuses(): void
    {
        $options = ServiceBooking::statusOptions();

        $this->assertCount(3, $options);
        $this->assertContains(ServiceBooking::STATUS_PENDING, $options);
        $this->assertContains(ServiceBooking::STATUS_CONFIRMED, $options);
        $this->assertContains(ServiceBooking::STATUS_REJECTED, $options);
    }
}
