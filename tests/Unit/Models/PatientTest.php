<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PatientTest extends TestCase
{
    public function test_initials_returns_first_letters_of_name(): void
    {
        $patient = new Patient;
        $patient->name = 'Ahmed Mohamed';

        $this->assertSame('AM', $patient->initials());
    }

    public function test_initials_with_single_name(): void
    {
        $patient = new Patient;
        $patient->name = 'Ahmed';

        $this->assertSame('A', $patient->initials());
    }

    public function test_initials_takes_only_two_parts(): void
    {
        $patient = new Patient;
        $patient->name = 'Ahmed Mohamed Ali';

        $this->assertSame('AM', $patient->initials());
    }

    public function test_initials_handles_extra_spaces(): void
    {
        $patient = new Patient;
        $patient->name = '  Ahmed   Mohamed  ';

        $this->assertSame('AM', $patient->initials());
    }

    public function test_age_returns_attribute_when_date_of_birth_is_null(): void
    {
        $patient = new Patient;
        $patient->forceFill(['age' => 30]);

        $this->assertSame(30, $patient->age());
    }

    public function test_age_returns_null_when_no_age_data(): void
    {
        $patient = new Patient;

        $this->assertNull($patient->age());
    }

    #[DataProvider('ageGroupProvider')]
    public function test_age_group(int $age, string $expected): void
    {
        $patient = new Patient;
        $patient->forceFill(['age' => $age]);

        $this->assertSame($expected, $patient->ageGroup());
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function ageGroupProvider(): array
    {
        return [
            'infant' => [1, 'child'],
            'child boundary' => [17, 'child'],
            'adult boundary low' => [18, 'adult'],
            'mid adult' => [35, 'adult'],
            'adult boundary high' => [59, 'adult'],
            'elderly boundary' => [60, 'elderly'],
            'senior' => [80, 'elderly'],
        ];
    }

    public function test_age_group_returns_unknown_when_age_is_null(): void
    {
        $patient = new Patient;

        $this->assertSame('unknown', $patient->ageGroup());
    }

    public function test_status_options_contains_expected_statuses(): void
    {
        $options = Patient::statusOptions();

        $this->assertContains(Patient::STATUS_ACTIVE, $options);
        $this->assertContains(Patient::STATUS_NEW, $options);
        $this->assertContains(Patient::STATUS_INACTIVE, $options);
        $this->assertCount(3, $options);
    }

    public function test_route_key_name_is_patient_code(): void
    {
        $patient = new Patient;

        $this->assertSame('patient_code', $patient->getRouteKeyName());
    }
}
