<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\PatientLookupService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PatientLookupServiceTest extends TestCase
{
    private PatientLookupService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PatientLookupService;
    }

    #[DataProvider('authorizeProvider')]
    public function test_authorize(string $role, string $context, bool $expected): void
    {
        $user = new User;
        $user->role = $role;

        $this->assertSame($expected, $this->service->authorize($user, $context));
    }

    /**
     * @return array<string, array{string, string, bool}>
     */
    public static function authorizeProvider(): array
    {
        return [
            'admin lab' => [User::ROLE_ADMIN, PatientLookupService::CONTEXT_LAB, true],
            'admin scan' => [User::ROLE_ADMIN, PatientLookupService::CONTEXT_SCAN, true],
            'admin pharmacy' => [User::ROLE_ADMIN, PatientLookupService::CONTEXT_PHARMACY, true],
            'admin reception' => [User::ROLE_ADMIN, PatientLookupService::CONTEXT_RECEPTION, true],
            'lab lab' => [User::ROLE_LAB, PatientLookupService::CONTEXT_LAB, true],
            'lab scan' => [User::ROLE_LAB, PatientLookupService::CONTEXT_SCAN, false],
            'lab pharmacy' => [User::ROLE_LAB, PatientLookupService::CONTEXT_PHARMACY, false],
            'scan_center scan' => [User::ROLE_SCAN_CENTER, PatientLookupService::CONTEXT_SCAN, true],
            'scan_center lab' => [User::ROLE_SCAN_CENTER, PatientLookupService::CONTEXT_LAB, false],
            'pharmacy pharmacy' => [User::ROLE_PHARMACY, PatientLookupService::CONTEXT_PHARMACY, true],
            'pharmacy lab' => [User::ROLE_PHARMACY, PatientLookupService::CONTEXT_LAB, false],
            'reception reception' => [User::ROLE_RECEPTION, PatientLookupService::CONTEXT_RECEPTION, true],
            'reception lab' => [User::ROLE_RECEPTION, PatientLookupService::CONTEXT_LAB, false],
            'patient lab' => [User::ROLE_PATIENT, PatientLookupService::CONTEXT_LAB, false],
            'patient reception' => [User::ROLE_PATIENT, PatientLookupService::CONTEXT_RECEPTION, false],
            'doctor lab' => [User::ROLE_DOCTOR, PatientLookupService::CONTEXT_LAB, false],
            'unknown context' => [User::ROLE_ADMIN, 'unknown', false],
        ];
    }

    public function test_normalize_trims_whitespace(): void
    {
        $this->assertSame('PAT-001', $this->service->normalize('  PAT-001  '));
    }

    public function test_normalize_returns_empty_for_empty_input(): void
    {
        $this->assertSame('', $this->service->normalize(''));
    }

    public function test_contexts_returns_all_valid_contexts(): void
    {
        $contexts = PatientLookupService::contexts();

        $this->assertCount(4, $contexts);
        $this->assertContains(PatientLookupService::CONTEXT_LAB, $contexts);
        $this->assertContains(PatientLookupService::CONTEXT_SCAN, $contexts);
        $this->assertContains(PatientLookupService::CONTEXT_PHARMACY, $contexts);
        $this->assertContains(PatientLookupService::CONTEXT_RECEPTION, $contexts);
    }
}
