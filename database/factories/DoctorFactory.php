<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected static ?int $doctorCodeCounter = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $department = Department::query()->inRandomOrder()->first();

        if (! $department) {
            return [];
        }

        $name = 'Dr. '.fake()->name();
        $avatarColumn = Schema::hasColumn('doctors', 'avatar') ? 'avatar' : 'avatar_path';

        return [
            'user_id' => User::factory()->doctor(),
            'department_id' => $department->id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.uniqid(),
            'doctor_code' => $this->nextDoctorCode(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'specialty' => fake()->randomElement($this->departmentProfile($department)['specialties']),
            'biography' => fake()->paragraph(),
            'address' => fake()->address(),
            'availability_status' => fake()->randomElement(Doctor::availabilityOptions()),
            'consultation_fee' => fake()->numberBetween($this->departmentProfile($department)['fee'][0], $this->departmentProfile($department)['fee'][1]),
            $avatarColumn => 'assets/images/profile/user-1.jpg',
            'years_of_experience' => fake()->numberBetween(3, 20),
        ];
    }

    private function departmentProfile(Department $department): array
    {
        return match ($department->name) {
            'General Medicine' => ['code' => 'GM', 'specialties' => ['General Physician', 'Family Medicine Specialist'], 'fee' => [150, 240]],
            'Cardiology' => ['code' => 'CD', 'specialties' => ['Heart Specialist', 'Cardiologist'], 'fee' => [280, 420]],
            'Pediatrics' => ['code' => 'PD', 'specialties' => ['Child Specialist', 'Pediatrician'], 'fee' => [180, 280]],
            'Dermatology' => ['code' => 'DM', 'specialties' => ['Skin Specialist', 'Dermatologist'], 'fee' => [180, 300]],
            'Neurology' => ['code' => 'NR', 'specialties' => ['Brain Specialist', 'Neurologist'], 'fee' => [300, 480]],
            'Orthopedics' => ['code' => 'OR', 'specialties' => ['Bone Specialist', 'Orthopedic Surgeon'], 'fee' => [240, 380]],
            default => ['code' => 'DR', 'specialties' => ['Medical Specialist'], 'fee' => [150, 350]],
        };
    }

    private function nextDoctorCode(): string
    {
        if (static::$doctorCodeCounter === null) {
            static::$doctorCodeCounter = Doctor::query()
                ->where('doctor_code', 'like', 'DR-%')
                ->pluck('doctor_code')
                ->map(fn (string $code) => (int) Str::afterLast($code, '-'))
                ->max() ?? 0;
        }

        do {
            $code = 'DR-'.++static::$doctorCodeCounter;
        } while (Doctor::query()->where('doctor_code', $code)->exists());

        return $code;
    }
}
