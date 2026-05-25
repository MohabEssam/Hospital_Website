<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaffLoginRedirectTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  callable(): User  $userFactory
     */
    #[DataProvider('staffDashboardProvider')]
    public function test_staff_users_are_redirected_to_their_role_dashboard_on_first_login(
        callable $userFactory,
        string $dashboardRoute,
    ): void {
        $user = $userFactory();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route($dashboardRoute));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @param  callable(): User  $userFactory
     */
    #[DataProvider('staffDashboardProvider')]
    public function test_staff_login_ignores_stale_forbidden_dashboard_intended_url(
        callable $userFactory,
        string $dashboardRoute,
    ): void {
        $user = $userFactory();

        $this->withSession(['url.intended' => route('dashboard')])
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect(route($dashboardRoute));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @return array<string, array{callable(): User, string}>
     */
    public static function staffDashboardProvider(): array
    {
        return [
            'lab staff' => [fn () => User::factory()->labStaff()->create(), 'lab.dashboard'],
            'scan staff' => [fn () => User::factory()->scanStaff()->create(), 'scan-center.dashboard'],
            'pharmacy staff' => [fn () => User::factory()->pharmacy()->create(), 'pharmacy.dashboard'],
        ];
    }
}
