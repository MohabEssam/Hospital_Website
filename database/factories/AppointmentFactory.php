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
        $doctor = Doctor::query()->inRandomOrder()->first() ?? Doctor::factory()->create();
        $patient = Patient::query()->inRandomOrder()->first() ?? Patient::factory()->create();

        $startHour = fake()->numberBetween(8, 15);
        $startTime = Carbon::createFromTime($startHour, fake()->randomElement([0, 30]));

        return [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'appointment_date' => fake()->dateTimeBetween('-3 months', '+3 months'),
            'start_time' => $startTime->format('H:i'),
            'end_time' => $startTime->copy()->addMinutes(30)->format('H:i'),
            'status' => Appointment::STATUS_CONFIRMED,
            'treatment' => fake()->randomElement(['Routine Check-Up', 'Cardiac Consultation', 'Pediatric Check-Up', 'Skin Allergy', 'Follow-Up Visit']),
            'notes' => fake()->optional()->sentence(),
            'fee' => max(0, (float) $doctor->consultation_fee + fake()->numberBetween(-25, 25)),
        ];
    }
}
