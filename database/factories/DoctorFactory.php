<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => 'Dr. '.fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'specialty' => fake()->jobTitle(),
            'biography' => fake()->paragraph(),
            'address' => fake()->address(),
            'availability_status' => fake()->randomElement(Doctor::availabilityOptions()),
            'consultation_fee' => fake()->numberBetween(150, 600),
            'avatar_path' => 'assets/images/profile/user-1.jpg',
            'years_of_experience' => fake()->numberBetween(3, 20),
        ];
    }
}
