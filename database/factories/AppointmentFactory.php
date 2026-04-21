<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 15);
        $startTime = Carbon::createFromTime($startHour, fake()->randomElement([0, 30]));

        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'appointment_date' => fake()->dateTimeBetween('-3 days', '+14 days'),
            'start_time' => $startTime->format('H:i'),
            'end_time' => $startTime->copy()->addMinutes(30)->format('H:i'),
            'status' => fake()->randomElement(Appointment::statusOptions()),
            'treatment' => fake()->randomElement(['Routine Check-Up', 'Cardiac Consultation', 'Pediatric Check-Up', 'Skin Allergy', 'Follow-Up Visit']),
            'notes' => fake()->optional()->sentence(),
            'fee' => fake()->numberBetween(150, 600),
        ];
    }
}
