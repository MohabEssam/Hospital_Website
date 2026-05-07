<?php

namespace Database\Seeders;

use App\Models\PatientCareService;
use Illuminate\Database\Seeder;

class PatientCareServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Cath Lab',
                'icon_class' => 'bi bi-heart-pulse-fill',
                'image' => 'assets/images/Patient Care/cath-lap-inner-side-image.png',
                'is_bookable' => false,
                'description' => 'Advanced cardiac catheterization laboratory equipped with state-of-the-art imaging systems for diagnostic and interventional heart procedures.',
                'content' => 'Our Cath Lab provides comprehensive cardiac catheterization services including coronary angiography, angioplasty, stent placement, and electrophysiology studies. Staffed by experienced interventional cardiologists and specialized nursing teams, we deliver precise diagnostics and life-saving interventions in a fully equipped, sterile environment.',
            ],
            [
                'name' => 'Clinics',
                'icon_class' => 'bi bi-building',
                'image' => 'assets/images/Patient Care/patient-care-clinics.jpg',
                'is_bookable' => true,
                'description' => 'Outpatient clinics offering specialized consultations across multiple medical disciplines with experienced physicians.',
                'content' => 'Medicare\'s outpatient clinics cover a broad spectrum of specialties including internal medicine, pediatrics, dermatology, ENT, orthopedics, and more. Each clinic is designed to provide comfortable, efficient consultations with minimal wait times. Our scheduling system ensures you see the right specialist at the right time.',
            ],
            [
                'name' => 'Day Care',
                'icon_class' => 'bi bi-sun-fill',
                'image' => 'assets/images/Patient Care/patient-care-side-service-page-final-edit.jpg',
                'is_bookable' => true,
                'description' => 'Same-day medical procedures and treatments that don\'t require overnight hospitalization.',
                'content' => 'Our Day Care unit handles minor surgical procedures, chemotherapy sessions, infusion therapy, and diagnostic procedures that allow patients to return home the same day. The unit features comfortable recovery bays, continuous monitoring, and dedicated nursing care to ensure safe and efficient same-day treatments.',
            ],
            [
                'name' => 'ER',
                'icon_class' => 'bi bi-plus-circle-fill',
                'image' => 'assets/images/Patient Care/patient-care-ER-final.jpg',
                'is_bookable' => false,
                'description' => '24/7 emergency department with rapid triage, trauma care, and critical life-saving interventions.',
                'content' => 'Our Emergency Room operates around the clock, 365 days a year, with a team of emergency medicine physicians, trauma surgeons, and critical care nurses. Equipped with advanced resuscitation bays, a dedicated trauma room, and direct access to imaging and lab services, we provide immediate care for all medical emergencies.',
            ],
            [
                'name' => 'ICU/CCU',
                'icon_class' => 'bi bi-activity',
                'image' => 'assets/images/Patient Care/patient-care-acu.jpg',
                'is_bookable' => false,
                'description' => 'Intensive and coronary care units providing 24/7 monitoring for critically ill patients.',
                'content' => 'The ICU and CCU at Medicare feature advanced life-support systems, continuous hemodynamic monitoring, mechanical ventilation, and round-the-clock intensivist coverage. Our multidisciplinary critical care team includes pulmonologists, cardiologists, and specially trained ICU nurses who deliver evidence-based care for the most complex medical conditions.',
            ],
            [
                'name' => 'Incubators',
                'icon_class' => 'bi bi-emoji-smile-fill',
                'image' => 'assets/images/Patient Care/patient-care-incubators.jpg',
                'is_bookable' => false,
                'description' => 'Neonatal intensive care with advanced incubator technology for premature and critically ill newborns.',
                'content' => 'Our NICU is equipped with the latest generation incubators providing precise temperature control, humidity regulation, and integrated monitoring for premature and critically ill newborns. A dedicated team of neonatologists, pediatric nurses, and respiratory therapists ensures the best possible outcomes for our youngest patients.',
            ],
            [
                'name' => 'Lab',
                'icon_class' => 'bi bi-droplet-fill',
                'image' => 'assets/images/Patient Care/patient-care-lab.jpg',
                'is_bookable' => true,
                'description' => 'Full-service clinical laboratory offering routine and specialized diagnostic tests with fast turnaround.',
                'content' => 'Medicare\'s clinical laboratory provides a comprehensive range of tests including hematology, biochemistry, microbiology, pathology, and molecular diagnostics. With automated analyzers and strict quality control protocols, we deliver accurate results with rapid turnaround times. Walk-in and appointment-based sample collection is available.',
            ],
            [
                'name' => 'Operation Theatres',
                'icon_class' => 'bi bi-scissors',
                'image' => 'assets/images/Patient Care/operation-threates-patient-care-side-image.png',
                'is_bookable' => false,
                'description' => 'Modern surgical suites equipped with advanced technology for a wide range of surgical procedures.',
                'content' => 'Our Operation Theatres feature laminar airflow systems, advanced anesthesia workstations, minimally invasive surgery equipment, and integrated digital imaging. From routine surgeries to complex multi-disciplinary procedures, our surgical teams work in a controlled, sterile environment designed for optimal patient safety and outcomes.',
            ],
            [
                'name' => 'Overnight Rooms',
                'icon_class' => 'bi bi-moon-stars-fill',
                'image' => 'assets/images/Patient Care/overnight-rooms-side.png',
                'is_bookable' => true,
                'description' => 'Comfortable inpatient rooms designed for recovery with 24-hour nursing care and modern amenities.',
                'content' => 'Our overnight rooms range from standard to VIP suites, all designed with patient comfort and recovery in mind. Each room features an adjustable hospital bed, nurse call system, en-suite bathroom, Wi-Fi, and television. Round-the-clock nursing care, meal service, and family visiting areas ensure a comfortable hospital stay.',
            ],
            [
                'name' => 'Pharmacy',
                'icon_class' => 'bi bi-capsule',
                'image' => 'assets/images/Patient Care/pharmacy-edit-29-9-last.jpg',
                'is_bookable' => false,
                'description' => 'In-house pharmacy providing prescription medications, over-the-counter products, and pharmaceutical counseling.',
                'content' => 'The Medicare Pharmacy is a fully stocked, hospital-based pharmacy staffed by licensed pharmacists. We dispense inpatient and outpatient medications, provide drug interaction checks, dosage guidance, and pharmaceutical counseling. Our inventory management system ensures medication availability and safety at all times.',
            ],
            [
                'name' => 'Physiotherapy',
                'icon_class' => 'bi bi-person-arms-up',
                'image' => 'assets/images/Patient Care/Physiotherapy-edit-29-9.jpg',
                'is_bookable' => true,
                'description' => 'Rehabilitation and physical therapy services for post-surgical recovery, sports injuries, and chronic conditions.',
                'content' => 'Our Physiotherapy department offers individualized rehabilitation programs designed by experienced physiotherapists. Services include post-operative rehabilitation, sports injury recovery, chronic pain management, neurological rehabilitation, and respiratory physiotherapy. Modern equipment and evidence-based techniques help patients regain mobility and function.',
            ],
            [
                'name' => 'Radiology',
                'icon_class' => 'bi bi-radioactive',
                'image' => 'assets/images/Patient Care/radiology-edit-29-9-last.jpg',
                'is_bookable' => true,
                'description' => 'Comprehensive diagnostic imaging services including X-ray, CT, MRI, ultrasound, and interventional radiology.',
                'content' => 'Medicare\'s Radiology department offers the full spectrum of diagnostic imaging: digital X-ray, multi-slice CT scanning, high-field MRI, ultrasound, mammography, and fluoroscopy. Our radiologists provide expert interpretations with rapid reporting. Appointment-based and urgent imaging services are available to support both outpatient and inpatient needs.',
            ],
        ];

        foreach ($services as $index => $data) {
            PatientCareService::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['sort_order' => $index + 1]),
            );
        }
    }
}
