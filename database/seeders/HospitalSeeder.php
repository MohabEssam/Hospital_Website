<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = collect([
            [
                'name' => 'General Medicine',
                'description' => 'Comprehensive primary care and preventive medicine for everyday health concerns.',
                'services' => ['Routine Check-Ups', 'Preventive Care', 'Chronic Disease Management', 'General Consultation'],
                'icon' => 'assets/images/Department/medicine-236x300.png',
                'hero_image' => 'assets/images/Department/medicine-870-336.jpg',
                'sidebar_image' => 'assets/images/Department/specialist-side-image.jpg',
            ],
            [
                'name' => 'Cardiology',
                'description' => 'Advanced heart and vascular care with diagnostics, monitoring, and specialist treatment plans.',
                'services' => ['Heart Disease Diagnostics', 'Hypertension Management', 'Cardiac Consultation', 'Valvular Interventions'],
                'icon' => 'assets/images/Department/card.png',
                'hero_image' => 'assets/images/Department/cardiology-edit.jpg',
                'sidebar_image' => 'assets/images/Department/specialist-side-image.jpg',
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Child-friendly medical care for infants, children, and adolescents.',
                'services' => ['Pediatric Check-Up', 'Growth Monitoring', 'Vaccination Support', 'Respiratory Care'],
                'icon' => 'assets/images/Department/ped.png',
                'hero_image' => 'assets/images/Department/pediatric.jpg',
                'sidebar_image' => 'assets/images/Department/specialist-side-image.jpg',
            ],
            [
                'name' => 'Dermatology',
                'description' => 'Diagnosis and treatment of skin, hair, and allergy-related conditions.',
                'services' => ['Skin Allergy', 'Dermatitis Care', 'Skin Consultation', 'Follow-Up Visits'],
                'icon' => 'assets/images/Department/dent.png',
                'hero_image' => 'assets/images/Department/dental.jpg',
                'sidebar_image' => 'assets/images/Department/specialist-side-image.jpg',
            ],
            [
                'name' => 'Neurology',
                'description' => 'Specialized care for disorders of the brain, spinal cord, and nervous system.',
                'services' => ['Brain Specialist Consults', 'Neurological Screening', 'Migraine Care', 'Follow-Up Monitoring'],
                'icon' => 'assets/images/Department/neurosurgery-home-icon.png',
                'hero_image' => 'assets/images/Department/neurosurgery-29-9-side-edit.jpg',
                'sidebar_image' => 'assets/images/Department/specialist-side-image.jpg',
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Bone, joint, and movement care with both surgical and non-surgical treatment plans.',
                'services' => ['Bone Specialist Consults', 'Joint Pain Care', 'Sports Injury Treatment', 'Post-Surgery Follow-Up'],
                'icon' => 'assets/images/Department/Orthopedic.png',
                'hero_image' => 'assets/images/Department/Orthopedic.jpg',
                'sidebar_image' => 'assets/images/Department/specialist-side-image.jpg',
            ],
        ])->mapWithKeys(function (array $department) {
            $createdDepartment = Department::create(array_merge($department, [
                'contact_phone' => '+20 2 1234 5678',
                'contact_email' => strtolower(str_replace(' ', '.', $department['name'])).'@medicare.test',
                'is_active' => true,
            ]));

            return [$department['name'] => $createdDepartment];
        });

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@medicare.test',
            'phone' => '+1 555-100-0000',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        $doctorUser = User::create([
            'name' => 'Dr. Petra Winsburry',
            'email' => 'doctor@medicare.test',
            'phone' => '+1 555-234-5678',
            'password' => Hash::make('password'),
            'role' => User::ROLE_DOCTOR,
            'email_verified_at' => now(),
        ]);

        $patientUser = User::create([
            'name' => 'Caren G. Simpson',
            'email' => 'patient@medicare.test',
            'phone' => '+1 555-000-1000',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PATIENT,
            'email_verified_at' => now(),
        ]);

        $doctors = collect([
            [
                'name' => 'Dr. Petra Winsburry',
                'user_id' => $doctorUser->id,
                'department' => 'General Medicine',
                'doctor_code' => 'WNH-GM-001',
                'email' => 'doctor@medicare.test',
                'phone' => '+1 555-234-5678',
                'specialty' => 'Routine Check-Ups',
                'biography' => 'Experienced general medicine practitioner focused on preventive care and routine health management.',
                'address' => 'WellNest Hospital, 456 Elm Street, Springfield, IL, USA',
                'availability_status' => Doctor::STATUS_AVAILABLE,
                'consultation_fee' => 180,
                'years_of_experience' => 15,
            ],
            [
                'name' => 'Dr. Olivia Martinez',
                'department' => 'Cardiology',
                'doctor_code' => 'WNH-CD-001',
                'email' => 'olivia.martinez@medicare.test',
                'phone' => '+1 555-234-5679',
                'specialty' => 'Heart Specialist',
                'biography' => 'Cardiology consultant specializing in heart disease diagnostics and chronic cardiac follow-up.',
                'address' => 'Medicare Hospital, Cardiology Wing',
                'availability_status' => Doctor::STATUS_UNAVAILABLE,
                'consultation_fee' => 320,
                'years_of_experience' => 12,
            ],
            [
                'name' => 'Dr. Damian Sanchez',
                'department' => 'Pediatrics',
                'doctor_code' => 'WNH-PD-001',
                'email' => 'damian.sanchez@medicare.test',
                'phone' => '+1 555-234-5680',
                'specialty' => 'Child Specialist',
                'biography' => 'Pediatric doctor delivering child-first care for routine and urgent consultations.',
                'address' => 'Medicare Hospital, Pediatric Floor',
                'availability_status' => Doctor::STATUS_AVAILABLE,
                'consultation_fee' => 210,
                'years_of_experience' => 10,
            ],
            [
                'name' => 'Dr. Chloe Harrington',
                'department' => 'Dermatology',
                'doctor_code' => 'WNH-DM-001',
                'email' => 'chloe.harrington@medicare.test',
                'phone' => '+1 555-234-5681',
                'specialty' => 'Skin Specialist',
                'biography' => 'Dermatology specialist treating allergies, irritation, and skin follow-up cases.',
                'address' => 'Medicare Hospital, Specialty Clinics',
                'availability_status' => Doctor::STATUS_AVAILABLE,
                'consultation_fee' => 190,
                'years_of_experience' => 9,
            ],
            [
                'name' => 'Dr. Emily Smith',
                'department' => 'Neurology',
                'doctor_code' => 'WNH-NR-001',
                'email' => 'emily.smith@medicare.test',
                'phone' => '+1 555-234-5682',
                'specialty' => 'Brain Specialist',
                'biography' => 'Neurology consultant managing diagnostic reviews and ongoing neurological follow-up.',
                'address' => 'Medicare Hospital, Neurology Center',
                'availability_status' => Doctor::STATUS_AVAILABLE,
                'consultation_fee' => 340,
                'years_of_experience' => 14,
            ],
            [
                'name' => 'Dr. Andrew Peterson',
                'department' => 'Orthopedics',
                'doctor_code' => 'WNH-OR-001',
                'email' => 'andrew.peterson@medicare.test',
                'phone' => '+1 555-234-5683',
                'specialty' => 'Bone Specialist',
                'biography' => 'Orthopedic consultant focused on bone, muscle, and mobility treatment plans.',
                'address' => 'Medicare Hospital, Orthopedic Suite',
                'availability_status' => Doctor::STATUS_AVAILABLE,
                'consultation_fee' => 260,
                'years_of_experience' => 11,
            ],
        ])->mapWithKeys(function (array $doctor) use ($departments) {
            $createdDoctor = Doctor::create([
                'user_id' => $doctor['user_id'] ?? null,
                'department_id' => $departments[$doctor['department']]->id,
                'name' => $doctor['name'],
                'doctor_code' => $doctor['doctor_code'],
                'email' => $doctor['email'],
                'phone' => $doctor['phone'],
                'specialty' => $doctor['specialty'],
                'biography' => $doctor['biography'],
                'address' => $doctor['address'],
                'availability_status' => $doctor['availability_status'],
                'consultation_fee' => $doctor['consultation_fee'],
                'avatar' => null,
                'years_of_experience' => $doctor['years_of_experience'],
            ]);

            return [$doctor['name'] => $createdDoctor];
        });

        $patients = collect([
            ['name' => 'Caren G. Simpson', 'email' => 'patient@medicare.test', 'age' => 35, 'doctor' => 'Dr. Petra Winsburry', 'treatment' => 'Routine Check-Up', 'room' => null, 'status' => Patient::STATUS_ACTIVE, 'user_id' => $patientUser->id],
            ['name' => 'Edgar Warrow', 'email' => 'edgar.warrow@medicare.test', 'age' => 45, 'doctor' => 'Dr. Olivia Martinez', 'treatment' => 'Cardiac Consultation', 'room' => null, 'status' => Patient::STATUS_ACTIVE],
            ['name' => 'Ocean Jane Lupre', 'email' => 'ocean.lupre@medicare.test', 'age' => 10, 'doctor' => 'Dr. Damian Sanchez', 'treatment' => 'Pediatric Check-Up', 'room' => 'Double 303', 'status' => Patient::STATUS_NEW],
            ['name' => 'Shane Riddick', 'email' => 'shane.riddick@medicare.test', 'age' => 50, 'doctor' => 'Dr. Chloe Harrington', 'treatment' => 'Skin Allergy', 'room' => 'Single 304', 'status' => Patient::STATUS_INACTIVE],
            ['name' => 'Queen Lawnston', 'email' => 'queen.lawnston@medicare.test', 'age' => 60, 'doctor' => 'Dr. Petra Winsburry', 'treatment' => 'Follow-Up Visit', 'room' => 'Single 305', 'status' => Patient::STATUS_ACTIVE],
            ['name' => 'Alice Mitchell', 'email' => 'alice.mitchell@medicare.test', 'age' => 28, 'doctor' => 'Dr. Emily Smith', 'treatment' => 'Routine Check-Up', 'room' => null, 'status' => Patient::STATUS_ACTIVE],
            ['name' => 'Mikhail Morozov', 'email' => 'mikhail.morozov@medicare.test', 'age' => 55, 'doctor' => 'Dr. Olivia Martinez', 'treatment' => 'Cardiac Consultation', 'room' => null, 'status' => Patient::STATUS_ACTIVE],
            ['name' => 'Mateus Fernandes', 'email' => 'mateus.fernandes@medicare.test', 'age' => 12, 'doctor' => 'Dr. Damian Sanchez', 'treatment' => 'Pediatric Check-Up', 'room' => 'Double 308', 'status' => Patient::STATUS_NEW],
            ['name' => 'Pari Desai', 'email' => 'pari.desai@medicare.test', 'age' => 40, 'doctor' => 'Dr. Chloe Harrington', 'treatment' => 'Skin Allergy', 'room' => 'Single 309', 'status' => Patient::STATUS_INACTIVE],
            ['name' => 'Omar Ali', 'email' => 'omar.ali@medicare.test', 'age' => 70, 'doctor' => 'Dr. Andrew Peterson', 'treatment' => 'Follow-Up Visit', 'room' => 'Single 310', 'status' => Patient::STATUS_ACTIVE],
            ['name' => 'Camila Alvarez', 'email' => 'camila.alvarez@medicare.test', 'age' => 30, 'doctor' => 'Dr. Olivia Martinez', 'treatment' => 'Cardiac Check-Up', 'room' => null, 'status' => Patient::STATUS_ACTIVE],
            ['name' => 'Thabo van Rooyen', 'email' => 'thabo.rooyen@medicare.test', 'age' => 15, 'doctor' => 'Dr. Damian Sanchez', 'treatment' => 'Pediatric Check-Up', 'room' => 'Double 312', 'status' => Patient::STATUS_NEW],
        ])->mapWithKeys(function (array $patient) use ($doctors) {
            $createdPatient = Patient::create([
                'user_id' => $patient['user_id'] ?? null,
                'doctor_id' => $doctors[$patient['doctor']]->id,
                'name' => $patient['name'],
                'email' => $patient['email'],
                'phone' => '+1 555-000-'.str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                'date_of_birth' => today()->subYears($patient['age'])->subDays(10),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'check_in_date' => today()->subDays(random_int(0, 7)),
                'treatment' => $patient['treatment'],
                'room_number' => $patient['room'],
                'status' => $patient['status'],
                'address' => '456 Elm Street, Springfield, IL, USA',
                'notes' => 'Seeded from the original dashboard sample data.',
                'avatar_path' => 'assets/images/profile/user-1.jpg',
            ]);

            return [$patient['name'] => $createdPatient];
        });

        $appointmentRows = [
            ['patient' => 'Caren G. Simpson', 'doctor' => 'Dr. Petra Winsburry', 'offset' => 0, 'start' => '09:00', 'treatment' => 'Routine Check-Up', 'status' => Appointment::STATUS_CONFIRMED],
            ['patient' => 'Edgar Warrow', 'doctor' => 'Dr. Olivia Martinez', 'offset' => 0, 'start' => '10:00', 'treatment' => 'Cardiac Consultation', 'status' => Appointment::STATUS_PENDING],
            ['patient' => 'Ocean Jane Lupre', 'doctor' => 'Dr. Damian Sanchez', 'offset' => 0, 'start' => '11:00', 'treatment' => 'Pediatric Check-Up', 'status' => Appointment::STATUS_CONFIRMED],
            ['patient' => 'Shane Riddick', 'doctor' => 'Dr. Chloe Harrington', 'offset' => 1, 'start' => '13:00', 'treatment' => 'Skin Allergy', 'status' => Appointment::STATUS_CANCELLED],
            ['patient' => 'Queen Lawnston', 'doctor' => 'Dr. Petra Winsburry', 'offset' => 1, 'start' => '14:00', 'treatment' => 'Follow-Up Visit', 'status' => Appointment::STATUS_CONFIRMED],
            ['patient' => 'Alice Mitchell', 'doctor' => 'Dr. Emily Smith', 'offset' => 2, 'start' => '09:30', 'treatment' => 'Routine Check-Up', 'status' => Appointment::STATUS_CONFIRMED],
            ['patient' => 'Mikhail Morozov', 'doctor' => 'Dr. Olivia Martinez', 'offset' => 2, 'start' => '11:30', 'treatment' => 'Cardiac Consultation', 'status' => Appointment::STATUS_PENDING],
            ['patient' => 'Mateus Fernandes', 'doctor' => 'Dr. Damian Sanchez', 'offset' => 3, 'start' => '10:00', 'treatment' => 'Pediatric Check-Up', 'status' => Appointment::STATUS_CONFIRMED],
            ['patient' => 'Pari Desai', 'doctor' => 'Dr. Chloe Harrington', 'offset' => 3, 'start' => '12:30', 'treatment' => 'Skin Allergy', 'status' => Appointment::STATUS_CANCELLED],
            ['patient' => 'Omar Ali', 'doctor' => 'Dr. Andrew Peterson', 'offset' => 4, 'start' => '15:00', 'treatment' => 'Follow-Up Visit', 'status' => Appointment::STATUS_CONFIRMED],
            ['patient' => 'Camila Alvarez', 'doctor' => 'Dr. Olivia Martinez', 'offset' => 5, 'start' => '10:30', 'treatment' => 'Cardiac Check-Up', 'status' => Appointment::STATUS_PENDING],
            ['patient' => 'Thabo van Rooyen', 'doctor' => 'Dr. Damian Sanchez', 'offset' => 6, 'start' => '09:00', 'treatment' => 'Pediatric Check-Up', 'status' => Appointment::STATUS_CONFIRMED],
        ];

        foreach ($appointmentRows as $row) {
            $start = Carbon::createFromFormat('H:i', $row['start']);

            Appointment::create([
                'patient_id' => $patients[$row['patient']]->id,
                'doctor_id' => $doctors[$row['doctor']]->id,
                'department_id' => $doctors[$row['doctor']]->department_id,
                'appointment_date' => today()->addDays($row['offset']),
                'start_time' => $start->format('H:i'),
                'end_time' => $start->copy()->addMinutes(30)->format('H:i'),
                'status' => $row['status'],
                'treatment' => $row['treatment'],
                'notes' => 'Seeded sample appointment.',
                'fee' => $doctors[$row['doctor']]->consultation_fee,
            ]);
        }

        Appointment::factory(8)->create();
    }
}
