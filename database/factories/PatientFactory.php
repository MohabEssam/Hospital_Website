<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected static ?int $patientCodeCounter = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $doctor = Doctor::query()
            ->whereIn('department_id', [1, 2, 3, 4, 5, 6])
            ->inRandomOrder()
            ->first();

        return [
            'user_id' => null,
            'doctor_id' => $doctor?->id ?? Doctor::factory(),
            'name' => fake()->name(),
            'patient_code' => $this->nextPatientCode(),
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

    public function withAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->patient(),
        ]);
    }

    public function withoutAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    private function nextPatientCode(): string
    {
        if (static::$patientCodeCounter === null) {
            static::$patientCodeCounter = Patient::query()
                ->pluck('patient_code')
                ->map(function (?string $code): int {
                    preg_match('/^PAT-(\d+)$/', (string) $code, $matches);

                    return (int) ($matches[1] ?? 0);
                })
                ->max() ?? 14;

            static::$patientCodeCounter = max(static::$patientCodeCounter, 14);
        }

        do {
            $code = 'PAT-'.str_pad((string) ++static::$patientCodeCounter, 4, '0', STR_PAD_LEFT);
        } while (Patient::query()->where('patient_code', $code)->exists());

        return $code;
    }
}
