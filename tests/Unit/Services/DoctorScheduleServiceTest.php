<?php

namespace Tests\Unit\Services;

use App\Models\DoctorSchedule;
use App\Services\DoctorScheduleService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DoctorScheduleServiceTest extends TestCase
{
    private DoctorScheduleService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DoctorScheduleService;
    }

    public function test_generate_slots_from_schedule_creates_correct_number_of_slots(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '09:00',
            'end_time' => '12:00',
            'is_available' => true,
        ]);

        $date = Carbon::parse('2026-07-01');
        $slots = $this->service->generateSlotsFromSchedule($schedule, [], $date);

        // 09:00, 09:30, 10:00, 10:30, 11:00, 11:30 = 6 slots of 30min in a 3-hour window
        $this->assertCount(6, $slots);
    }

    public function test_generate_slots_from_schedule_returns_correct_times(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '14:00',
            'end_time' => '15:30',
        ]);

        $date = Carbon::parse('2026-07-01');
        $slots = $this->service->generateSlotsFromSchedule($schedule, [], $date);

        $times = array_column($slots, 'time');
        $this->assertSame(['14:00', '14:30', '15:00'], $times);
    }

    public function test_generate_slots_marks_booked_times_as_unavailable(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);

        $date = Carbon::parse('2026-07-01');
        $bookedTimes = ['09:30'];
        $slots = $this->service->generateSlotsFromSchedule($schedule, $bookedTimes, $date);

        $slotsByTime = array_column($slots, null, 'time');

        $this->assertTrue($slotsByTime['09:00']['available']);
        $this->assertFalse($slotsByTime['09:30']['available']);
        $this->assertSame('Booked', $slotsByTime['09:30']['reason']);
        $this->assertTrue($slotsByTime['10:00']['available']);
    }

    public function test_generate_slots_marks_past_times_as_unavailable_for_today(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '00:00',
            'end_time' => '23:59',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-01 10:15'));
        $date = Carbon::today();
        $slots = $this->service->generateSlotsFromSchedule($schedule, [], $date);

        $slotsByTime = array_column($slots, null, 'time');

        // 09:00 and 10:00 are past (10:00 <= 10:15)
        $this->assertFalse($slotsByTime['09:00']['available']);
        $this->assertSame('Past', $slotsByTime['09:00']['reason']);
        $this->assertFalse($slotsByTime['10:00']['available']);

        // 10:30 is future
        $this->assertTrue($slotsByTime['10:30']['available']);
        $this->assertNull($slotsByTime['10:30']['reason']);

        Carbon::setTestNow();
    }

    public function test_generate_slots_returns_empty_when_window_too_small(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '09:00',
            'end_time' => '09:15',
        ]);

        $date = Carbon::parse('2026-07-01');
        $slots = $this->service->generateSlotsFromSchedule($schedule, [], $date);

        $this->assertEmpty($slots);
    }

    public function test_generate_slots_includes_slot_when_exactly_one_duration(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '09:00',
            'end_time' => '09:30',
        ]);

        $date = Carbon::parse('2026-07-01');
        $slots = $this->service->generateSlotsFromSchedule($schedule, [], $date);

        $this->assertCount(1, $slots);
        $this->assertSame('09:00', $slots[0]['time']);
    }

    public function test_generate_slots_produces_readable_labels(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '14:00',
            'end_time' => '15:00',
        ]);

        $date = Carbon::parse('2026-07-01');
        $slots = $this->service->generateSlotsFromSchedule($schedule, [], $date);

        $labels = array_column($slots, 'label');
        $this->assertSame(['2:00 PM', '2:30 PM'], $labels);
    }

    public function test_slot_duration_is_thirty_minutes(): void
    {
        $this->assertSame(30, DoctorScheduleService::SLOT_DURATION_MINUTES);
    }

    public function test_available_slot_has_null_reason(): void
    {
        $schedule = new DoctorSchedule([
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $date = Carbon::parse('2026-07-01');
        $slots = $this->service->generateSlotsFromSchedule($schedule, [], $date);

        foreach ($slots as $slot) {
            $this->assertNull($slot['reason']);
        }
    }
}
