<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_has_role_returns_true_for_matching_role(): void
    {
        $user = new User(['role' => User::ROLE_ADMIN]);
        $user->role = User::ROLE_ADMIN;

        $this->assertTrue($user->hasRole(User::ROLE_ADMIN));
    }

    public function test_has_role_returns_false_for_different_role(): void
    {
        $user = new User;
        $user->role = User::ROLE_PATIENT;

        $this->assertFalse($user->hasRole(User::ROLE_ADMIN));
    }

    public function test_has_any_role_returns_true_when_role_is_in_list(): void
    {
        $user = new User;
        $user->role = User::ROLE_LAB;

        $this->assertTrue($user->hasAnyRole([User::ROLE_LAB, User::ROLE_PHARMACY]));
    }

    public function test_has_any_role_returns_false_when_role_is_not_in_list(): void
    {
        $user = new User;
        $user->role = User::ROLE_DOCTOR;

        $this->assertFalse($user->hasAnyRole([User::ROLE_LAB, User::ROLE_PHARMACY]));
    }

    public function test_has_any_role_returns_false_for_empty_list(): void
    {
        $user = new User;
        $user->role = User::ROLE_ADMIN;

        $this->assertFalse($user->hasAnyRole([]));
    }

    #[DataProvider('roleCheckProvider')]
    public function test_role_check_methods(string $role, string $method, bool $expected): void
    {
        $user = new User;
        $user->role = $role;

        $this->assertSame($expected, $user->{$method}());
    }

    /**
     * @return array<string, array{string, string, bool}>
     */
    public static function roleCheckProvider(): array
    {
        return [
            'admin isAdmin' => [User::ROLE_ADMIN, 'isAdmin', true],
            'patient isAdmin' => [User::ROLE_PATIENT, 'isAdmin', false],
            'doctor isDoctor' => [User::ROLE_DOCTOR, 'isDoctor', true],
            'admin isDoctor' => [User::ROLE_ADMIN, 'isDoctor', false],
            'patient isPatient' => [User::ROLE_PATIENT, 'isPatient', true],
            'doctor isPatient' => [User::ROLE_DOCTOR, 'isPatient', false],
            'lab isLab' => [User::ROLE_LAB, 'isLab', true],
            'patient isLab' => [User::ROLE_PATIENT, 'isLab', false],
            'scan_center isScanCenter' => [User::ROLE_SCAN_CENTER, 'isScanCenter', true],
            'lab isScanCenter' => [User::ROLE_LAB, 'isScanCenter', false],
            'pharmacy isPharmacy' => [User::ROLE_PHARMACY, 'isPharmacy', true],
            'admin isPharmacy' => [User::ROLE_ADMIN, 'isPharmacy', false],
            'reception isReception' => [User::ROLE_RECEPTION, 'isReception', true],
            'pharmacy isReception' => [User::ROLE_PHARMACY, 'isReception', false],
            'lab isLabStaff' => [User::ROLE_LAB, 'isLabStaff', true],
            'scan_center isScanStaff' => [User::ROLE_SCAN_CENTER, 'isScanStaff', true],
        ];
    }

    #[DataProvider('prefixForRoleProvider')]
    public function test_prefix_for_role(string $role, string $expectedPrefix): void
    {
        $this->assertSame($expectedPrefix, User::prefixForRole($role));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function prefixForRoleProvider(): array
    {
        return [
            'admin' => [User::ROLE_ADMIN, 'ADM'],
            'doctor' => [User::ROLE_DOCTOR, 'DR'],
            'patient' => [User::ROLE_PATIENT, 'PAT'],
            'lab' => [User::ROLE_LAB, 'LAB'],
            'pharmacy' => [User::ROLE_PHARMACY, 'PH'],
            'scan_center' => [User::ROLE_SCAN_CENTER, 'SCAN'],
            'reception' => [User::ROLE_RECEPTION, 'REC'],
            'unknown' => ['unknown_role', 'USR'],
        ];
    }

    public function test_roles_returns_all_defined_roles(): void
    {
        $roles = User::roles();

        $this->assertContains(User::ROLE_ADMIN, $roles);
        $this->assertContains(User::ROLE_DOCTOR, $roles);
        $this->assertContains(User::ROLE_PATIENT, $roles);
        $this->assertContains(User::ROLE_PHARMACY, $roles);
        $this->assertContains(User::ROLE_LAB, $roles);
        $this->assertContains(User::ROLE_SCAN_CENTER, $roles);
        $this->assertContains(User::ROLE_RECEPTION, $roles);
        $this->assertCount(7, $roles);
    }

    public function test_genders_returns_male_and_female(): void
    {
        $genders = User::genders();

        $this->assertSame([User::GENDER_MALE, User::GENDER_FEMALE], $genders);
    }
}
