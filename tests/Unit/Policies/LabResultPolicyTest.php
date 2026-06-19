<?php

namespace Tests\Unit\Policies;

use App\Models\LabResult;
use App\Models\User;
use App\Policies\LabResultPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LabResultPolicyTest extends TestCase
{
    private LabResultPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new LabResultPolicy;
    }

    #[DataProvider('viewAnyProvider')]
    public function test_view_any(string $role, bool $expected): void
    {
        $user = new User;
        $user->role = $role;

        $this->assertSame($expected, $this->policy->viewAny($user));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function viewAnyProvider(): array
    {
        return [
            'admin can view any' => [User::ROLE_ADMIN, true],
            'doctor can view any' => [User::ROLE_DOCTOR, true],
            'lab can view any' => [User::ROLE_LAB, true],
            'patient cannot view any' => [User::ROLE_PATIENT, false],
            'pharmacy cannot view any' => [User::ROLE_PHARMACY, false],
            'scan center cannot view any' => [User::ROLE_SCAN_CENTER, false],
            'reception cannot view any' => [User::ROLE_RECEPTION, false],
        ];
    }

    public function test_create_allowed_for_admin(): void
    {
        $user = new User;
        $user->role = User::ROLE_ADMIN;

        $this->assertTrue($this->policy->create($user));
    }

    public function test_create_allowed_for_lab(): void
    {
        $user = new User;
        $user->role = User::ROLE_LAB;

        $this->assertTrue($this->policy->create($user));
    }

    public function test_create_denied_for_patient(): void
    {
        $user = new User;
        $user->role = User::ROLE_PATIENT;

        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_allowed_for_admin(): void
    {
        $user = new User;
        $user->role = User::ROLE_ADMIN;

        $this->assertTrue($this->policy->update($user, new LabResult));
    }

    public function test_update_allowed_for_lab(): void
    {
        $user = new User;
        $user->role = User::ROLE_LAB;

        $this->assertTrue($this->policy->update($user, new LabResult));
    }

    public function test_update_denied_for_doctor(): void
    {
        $user = new User;
        $user->role = User::ROLE_DOCTOR;

        $this->assertFalse($this->policy->update($user, new LabResult));
    }

    public function test_delete_allowed_for_admin_only(): void
    {
        $admin = new User;
        $admin->role = User::ROLE_ADMIN;

        $lab = new User;
        $lab->role = User::ROLE_LAB;

        $this->assertTrue($this->policy->delete($admin, new LabResult));
        $this->assertFalse($this->policy->delete($lab, new LabResult));
    }
}
