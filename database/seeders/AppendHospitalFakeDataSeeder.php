<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class AppendHospitalFakeDataSeeder extends Seeder
{
    private const AVATAR_PATH = 'assets/images/profile/user-1.jpg';

    public function run(): void
    {
        DB::transaction(function (): void {
            $departmentIds = DB::table('departments')->pluck('id')->toArray();
            $doctorCodeCounters = [];
            $nextPatientNumber = $this->nextPatientNumber();

            if (empty($departmentIds)) {
                Log::warning('No departments found. Skipping doctor creation in append hospital fake data seeder.');
            } else {
                $this->createDoctorUsersAndDoctors($departmentIds, $doctorCodeCounters);
            }

            $doctors = Doctor::query()->with('department')->get();

            $this->createPatientUsersAndPatients($doctors, $nextPatientNumber);
            $this->createPatientsWithoutAccounts($doctors, $nextPatientNumber);

            $doctors = Doctor::query()->with('department')->get()->shuffle()->values();
            $patients = Patient::query()->get()->shuffle()->values();

            $this->createAppointments($doctors, $patients);
        });
    }

    private function createDoctorUsersAndDoctors(array $departmentIds, array &$doctorCodeCounters): void
    {
        $departments = Department::query()
            ->whereKey($departmentIds)
            ->get()
            ->keyBy('id');

        for ($i = 0; $i < 20; $i++) {
            $departmentId = $departmentIds[$i % count($departmentIds)];
            $department = $departments->get($departmentId);
            $profile = $this->departmentProfile($department);
            $name = 'Dr. '.fake()->unique()->name();
            $email = $this->uniqueEmail('doctor', $name);

            $user = User::factory()
                ->doctor()
                ->create([
                    'name' => $name,
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                ]);

            Doctor::factory()->create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'name' => $name,
                'slug' => Str::slug($name).'-'.uniqid(),
                'doctor_code' => $this->nextDoctorCode($profile['code'], $doctorCodeCounters),
                'email' => $email,
                'phone' => fake()->phoneNumber(),
                'specialty' => fake()->randomElement($profile['specialties']),
                'biography' => fake()->paragraph(),
                'address' => fake()->address(),
                'availability_status' => fake()->randomElement(Doctor::availabilityOptions()),
                'consultation_fee' => fake()->numberBetween($profile['fee'][0], $profile['fee'][1]),
                $this->doctorAvatarColumn() => self::AVATAR_PATH,
                'years_of_experience' => fake()->numberBetween(3, 30),
                'rating' => fake()->randomFloat(1, 4.1, 5.0),
            ]);
        }
    }

    private function createPatientUsersAndPatients(Collection $doctors, int &$nextPatientNumber): void
    {
        for ($i = 0; $i < 80; $i++) {
            $doctor = $doctors[$i % $doctors->count()];
            $profile = $this->departmentProfile($doctor->department);
            $name = fake()->name();
            $email = $this->uniqueEmail('patient', $name);

            $user = User::factory()
                ->patient()
                ->create([
                    'name' => $name,
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                ]);

            Patient::factory()->create([
                'user_id' => $user->id,
                'doctor_id' => $doctor->id,
                'name' => $name,
                'patient_code' => $this->nextPatientCode($nextPatientNumber),
                'email' => $email,
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->dateTimeBetween('-85 years', '-1 years'),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'check_in_date' => fake()->dateTimeBetween('-3 months', 'now'),
                'treatment' => fake()->randomElement($profile['treatments']),
                'room_number' => fake()->optional(0.35)->numerify('Room ###'),
                'status' => fake()->randomElement(Patient::statusOptions()),
                'address' => fake()->address(),
                'notes' => fake()->optional()->sentence(),
                'avatar_path' => self::AVATAR_PATH,
            ]);
        }
    }

    private function createPatientsWithoutAccounts(Collection $doctors, int &$nextPatientNumber): void
    {
        for ($i = 0; $i < 70; $i++) {
            $doctor = $doctors[$i % $doctors->count()];
            $profile = $this->departmentProfile($doctor->department);
            $name = fake()->name();

            Patient::factory()->create([
                'user_id' => null,
                'doctor_id' => $doctor->id,
                'name' => $name,
                'patient_code' => $this->nextPatientCode($nextPatientNumber),
                'email' => $this->uniqueEmail('walkin', $name),
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->dateTimeBetween('-85 years', '-1 years'),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'check_in_date' => fake()->dateTimeBetween('-3 months', 'now'),
                'treatment' => fake()->randomElement($profile['treatments']),
                'room_number' => fake()->optional(0.35)->numerify('Room ###'),
                'status' => fake()->randomElement(Patient::statusOptions()),
                'address' => fake()->address(),
                'notes' => fake()->optional()->sentence(),
                'avatar_path' => self::AVATAR_PATH,
            ]);
        }
    }

    private function createAppointments(Collection $doctors, Collection $patients): void
    {
        if ($doctors->isEmpty() || $patients->isEmpty()) {
            throw new RuntimeException('Doctors and patients must exist before creating appointments.');
        }

        for ($i = 0; $i < 300; $i++) {
            $doctor = $doctors[$i % $doctors->count()];
            $patient = $patients[$i % $patients->count()];
            $profile = $this->departmentProfile($doctor->department);
            $startTime = Carbon::createFromTime(fake()->numberBetween(8, 15), fake()->randomElement([0, 30]));
            $fee = max(0, (float) $doctor->consultation_fee + fake()->numberBetween(-25, 25));

            Appointment::factory()->create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'department_id' => $doctor->department_id,
                'appointment_date' => fake()->dateTimeBetween('-3 months', '+3 months'),
                'start_time' => $startTime->format('H:i'),
                'end_time' => $startTime->copy()->addMinutes(30)->format('H:i'),
                'status' => fake()->randomElement(Appointment::statusOptions()),
                'treatment' => fake()->randomElement($profile['treatments']),
                'notes' => fake()->optional()->sentence(),
                'fee' => $fee,
            ]);
        }
    }

    private function departmentProfile(?Department $department): array
    {
        return match ($department?->name) {
            'General Medicine' => [
                'code' => 'GM',
                'specialties' => ['General Physician', 'Family Medicine Specialist', 'Internal Medicine Specialist'],
                'treatments' => ['Routine Check-Up', 'Preventive Care', 'Chronic Disease Management', 'General Consultation'],
                'fee' => [150, 240],
            ],
            'Cardiology' => [
                'code' => 'CD',
                'specialties' => ['Heart Specialist', 'Cardiologist', 'Cardiac Consultant'],
                'treatments' => ['Cardiac Consultation', 'Heart Disease Diagnostics', 'Hypertension Management', 'Cardiac Check-Up'],
                'fee' => [280, 420],
            ],
            'Pediatrics' => [
                'code' => 'PD',
                'specialties' => ['Child Specialist', 'Pediatrician', 'Child Care Consultant'],
                'treatments' => ['Pediatric Check-Up', 'Growth Monitoring', 'Vaccination Support', 'Respiratory Care'],
                'fee' => [180, 280],
            ],
            'Dermatology' => [
                'code' => 'DM',
                'specialties' => ['Skin Specialist', 'Dermatologist', 'Allergy Skin Consultant'],
                'treatments' => ['Skin Allergy', 'Dermatitis Care', 'Skin Consultation', 'Follow-Up Visits'],
                'fee' => [180, 300],
            ],
            'Neurology' => [
                'code' => 'NR',
                'specialties' => ['Brain Specialist', 'Neurologist', 'Nerve Care Specialist'],
                'treatments' => ['Brain Specialist Consults', 'Neurological Screening', 'Migraine Care', 'Follow-Up Monitoring'],
                'fee' => [300, 480],
            ],
            'Orthopedics' => [
                'code' => 'OR',
                'specialties' => ['Bone Specialist', 'Orthopedic Surgeon', 'Joint Care Specialist'],
                'treatments' => ['Bone Specialist Consults', 'Joint Pain Care', 'Sports Injury Treatment', 'Post-Surgery Follow-Up'],
                'fee' => [240, 380],
            ],
            default => [
                'code' => 'DR',
                'specialties' => ['Medical Specialist'],
                'treatments' => ['General Consultation', 'Follow-Up Visit'],
                'fee' => [150, 350],
            ],
        };
    }

    private function nextDoctorCode(string $departmentCode, array &$doctorCodeCounters): string
    {
        if (! array_key_exists($departmentCode, $doctorCodeCounters)) {
            $doctorCodeCounters[$departmentCode] = Doctor::query()
                ->where('doctor_code', 'like', "WNH-{$departmentCode}-%")
                ->pluck('doctor_code')
                ->map(fn (string $code) => (int) Str::afterLast($code, '-'))
                ->max() ?? 0;
        }

        do {
            $code = sprintf('WNH-%s-%03d', $departmentCode, ++$doctorCodeCounters[$departmentCode]);
        } while (Doctor::query()->where('doctor_code', $code)->exists());

        return $code;
    }

    private function nextPatientNumber(): int
    {
        $max = Patient::query()
            ->pluck('patient_code')
            ->map(function (?string $code): int {
                preg_match('/^PAT-(\d+)$/', (string) $code, $matches);

                return (int) ($matches[1] ?? 0);
            })
            ->max() ?? 14;

        return max($max + 1, 15);
    }

    private function nextPatientCode(int &$nextPatientNumber): string
    {
        do {
            $code = 'PAT-'.str_pad((string) $nextPatientNumber++, 4, '0', STR_PAD_LEFT);
        } while (Patient::query()->where('patient_code', $code)->exists());

        return $code;
    }

    private function uniqueEmail(string $type, string $name): string
    {
        do {
            $email = (Str::slug($name) ?: $type).'.'.$type.'.'.uniqid().'@medicare.test';
        } while (
            User::query()->where('email', $email)->exists()
            || Doctor::query()->where('email', $email)->exists()
            || Patient::query()->where('email', $email)->exists()
        );

        return $email;
    }

    private function doctorAvatarColumn(): string
    {
        return Schema::hasColumn('doctors', 'avatar') ? 'avatar' : 'avatar_path';
    }
}
