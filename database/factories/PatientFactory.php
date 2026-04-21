<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-5 years'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'check_in_date' => fake()->dateTimeBetween('-20 days', 'now'),
            'treatment' => fake()->randomElement(['Routine Check-Up', 'Cardiac Consultation', 'Pediatric Check-Up', 'Skin Allergy', 'Follow-Up Visit']),
            'room_number' => fake()->optional()->numerify('Room ###'),
            'status' => fake()->randomElement(Patient::statusOptions()),
            'address' => fake()->address(),
            'notes' => fake()->optional()->sentence(),
            'avatar_path' => 'assets/images/profile/user-1.jpg',
        ];
    }
}
