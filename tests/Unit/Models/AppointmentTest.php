<?php

namespace Tests\Unit\Models;

use App\Models\Appointment;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AppointmentTest extends TestCase
{
    public function test_is_cancelled_returns_true_for_cancelled_status(): void
    {
        $appointment = new Appointment;
        $appointment->status = Appointment::STATUS_CANCELLED;

        $this->assertTrue($appointment->isCancelled());
    }

    #[DataProvider('nonCancelledStatusProvider')]
    public function test_is_cancelled_returns_false_for_other_statuses(string $status): void
    {
        $appointment = new Appointment;
        $appointment->status = $status;

        $this->assertFalse($appointment->isCancelled());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function nonCancelledStatusProvider(): array
    {
        return [
            'pending' => [Appointment::STATUS_PENDING],
            'confirmed' => [Appointment::STATUS_CONFIRMED],
            'rejected' => [Appointment::STATUS_REJECTED],
            'completed' => [Appointment::STATUS_COMPLETED],
        ];
    }

    public function test_blocks_slot_returns_true_for_pending(): void
    {
        $appointment = new Appointment;
        $appointment->status = Appointment::STATUS_PENDING;

        $this->assertTrue($appointment->blocksSlot());
    }

    public function test_blocks_slot_returns_true_for_confirmed(): void
    {
        $appointment = new Appointment;
        $appointment->status = Appointment::STATUS_CONFIRMED;

        $this->assertTrue($appointment->blocksSlot());
    }

    #[DataProvider('nonBlockingStatusProvider')]
    public function test_blocks_slot_returns_false_for_non_blocking_statuses(string $status): void
    {
        $appointment = new Appointment;
        $appointment->status = $status;

        $this->assertFalse($appointment->blocksSlot());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function nonBlockingStatusProvider(): array
    {
        return [
            'rejected' => [Appointment::STATUS_REJECTED],
            'cancelled' => [Appointment::STATUS_CANCELLED],
            'completed' => [Appointment::STATUS_COMPLETED],
        ];
    }

    public function test_slot_blocking_statuses_returns_pending_and_confirmed(): void
    {
        $statuses = Appointment::slotBlockingStatuses();

        $this->assertSame([Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED], $statuses);
    }

    public function test_status_options_contains_all_statuses(): void
    {
        $options = Appointment::statusOptions();

        $this->assertCount(5, $options);
        $this->assertContains(Appointment::STATUS_PENDING, $options);
        $this->assertContains(Appointment::STATUS_CONFIRMED, $options);
        $this->assertContains(Appointment::STATUS_REJECTED, $options);
        $this->assertContains(Appointment::STATUS_CANCELLED, $options);
        $this->assertContains(Appointment::STATUS_COMPLETED, $options);
    }
}
