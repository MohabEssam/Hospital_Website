<?php

namespace Tests\Feature\Auth;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_patient_registration_requires_gender(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Taylor Patient',
            'email' => 'taylor@example.test',
            'phone' => '01012345678',
            'age' => 32,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('gender');
    }

    public function test_patient_registration_requires_phone(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Taylor Patient',
            'email' => 'taylor@example.test',
            'gender' => User::GENDER_FEMALE,
            'age' => 32,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('phone');
    }

    public function test_patient_registration_stores_user_and_patient_phone_and_gender(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Taylor Patient',
            'email' => 'taylor@example.test',
            'phone' => '01012345678',
            'gender' => User::GENDER_FEMALE,
            'age' => 32,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'taylor@example.test',
            'phone' => '01012345678',
            'gender' => User::GENDER_FEMALE,
            'role' => User::ROLE_PATIENT,
        ]);
        $this->assertDatabaseHas('patients', [
            'email' => 'taylor@example.test',
            'phone' => '01012345678',
            'gender' => 'Female',
            'status' => Patient::STATUS_NEW,
        ]);
    }
}
