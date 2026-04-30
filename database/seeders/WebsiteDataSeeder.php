<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Lab;
use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class WebsiteDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedHospitals();
        $this->seedLabs();
        $this->seedPharmacies();
    }

    private function seedHospitals(): void
    {
        $hospitals = [
            [
                'name' => 'Al Salam International Hospital',
                'slug' => 'al-salam-international-hospital',
                'description' => 'Al Salam International Hospital is a first-class medical institution that provides high-quality healthcare services to patients from all over the world. The hospital has many specialized departments and highly qualified medical staff, which makes it one of the best.',
                'email' => 'info@assih.com',
                'phone' => '19885',
                'address' => 'Corniche El Nile, Athar an Nabi, Misr Al Qadimah, Cairo Governorate, Cairo, Egypt',
                'image' => 'alsalam(2).jpeg',
                'doctors' => [
                    ['name' => 'Dr. Ahmed Shokry', 'department' => 'Cardiology', 'days' => 'Tuesday to Friday', 'hours' => '7am to 5pm'],
                    ['name' => 'Dr. Anwar Elshenawy', 'department' => 'Surgery', 'days' => 'Sunday to Thursday', 'hours' => '5am to 12pm'],
                    ['name' => 'Dr. Ziad Samir', 'department' => 'Neurology', 'days' => 'Thursday to Monday', 'hours' => '11am to 9pm'],
                    ['name' => 'Dr. Ahmed Elsayed', 'department' => 'Pediatric', 'days' => 'Monday to Saturday', 'hours' => '9am to 2pm'],
                    ['name' => 'Dr. Ahmed Gamal', 'department' => 'Ear, Nose, and Throat', 'days' => 'Saturday to Tuesday', 'hours' => '6am to 3pm'],
                    ['name' => 'Dr. Mohamed Esmail', 'department' => 'Orthopedics', 'days' => 'Wednesday to Monday', 'hours' => '9am to 7pm'],
                    ['name' => 'Dr. AMR Aboelala', 'department' => 'Ophthalmology', 'days' => 'Monday to Friday', 'hours' => '8am to 2pm'],
                    ['name' => 'Dr. Doaa Mansour', 'department' => 'Dentistry', 'days' => 'Sunday to Friday', 'hours' => '11am to 5pm'],
                    ['name' => 'Dr. Alaa Husein', 'department' => 'Dermatology', 'days' => 'Saturday to Tuesday', 'hours' => '8am to 2pm'],
                    ['name' => 'Dr. Ali Zedan', 'department' => 'Psychiatry', 'days' => 'Sunday to Friday', 'hours' => '11am to 5pm'],
                    ['name' => 'Dr. Mohamed Samir', 'department' => 'Emergency', 'days' => 'Saturday to Friday', 'hours' => '9pm to 3am'],
                    ['name' => 'Dr. Mohamed Abdullah', 'department' => 'Nutrition Consultant', 'days' => 'Sunday to Wednesday', 'hours' => '12am to 6am'],
                    ['name' => 'Dr. Marwa Sabry', 'department' => 'Speech Therapy', 'days' => 'Monday to Friday', 'hours' => '9am to 6pm'],
                    ['name' => 'Dr. Amr Eldeep', 'department' => 'Consultant of Urology', 'days' => 'Sunday to Thursday', 'hours' => '12pm to 8pm'],
                    ['name' => 'Dr. Mohamed Helal', 'department' => 'Physiotherapy', 'days' => 'Sunday to Monday', 'hours' => '7am to 5pm'],
                    ['name' => 'Dr. Abeer Ali', 'department' => 'Internal Medicine', 'days' => 'Wednesday to Sunday', 'hours' => '10am to 6pm'],
                    ['name' => 'Dr. Tarek Mohsen', 'department' => 'Senior Non-Invasive', 'days' => 'Sunday to Thursday', 'hours' => '8am to 2pm'],
                    ['name' => 'Dr. Hala Zaki', 'department' => 'Cardiology', 'days' => 'Sunday to Friday', 'hours' => '1pm to 12am'],
                ],
            ],
            [
                'name' => 'Dar Al Fouad Hospital',
                'slug' => 'dar-al-fouad-hospital',
                'description' => 'Dar Al Fouad Hospital is a general hospital in Giza, Egypt. Founded in 1957 by Dr. Muhammad Abu Al-Futouh. The hospital has 350 beds and offers a wide range of medical services, including cardiology, orthopedics, and many more specialties.',
                'email' => 'info@daralfouad.org',
                'phone' => '16370',
                'address' => 'Intersection of Nasr Road with Youssef Abbas Street, Nasr City, Cairo, Egypt',
                'image' => 'dar alfouad (1).png',
                'doctors' => [
                    ['name' => 'Dr. Nabil Farag', 'department' => 'Cardiology', 'days' => 'Sunday to Friday', 'hours' => '9am to 5pm'],
                    ['name' => 'Dr. Osama El Malt', 'department' => 'Surgery', 'days' => 'Sunday to Thursday', 'hours' => '10am to 6pm'],
                    ['name' => 'Dr. Mohamed S. Bassiouny', 'department' => 'Neurology', 'days' => 'Saturday to Friday', 'hours' => '9am to 3pm'],
                    ['name' => 'Dr. Nabil El Desouki', 'department' => 'Pediatric', 'days' => 'Monday to Saturday', 'hours' => '7am to 2pm'],
                    ['name' => 'Dr. Fatema Al Zahraa Saad', 'department' => 'Ear, Nose, and Throat', 'days' => 'Sunday to Friday', 'hours' => '9am to 5pm'],
                    ['name' => 'Dr. Ayman Yosry', 'department' => 'Orthopedics', 'days' => 'Wednesday to Monday', 'hours' => '9am to 7pm'],
                    ['name' => 'Dr. Amr A. Gad', 'department' => 'Ophthalmology', 'days' => 'Saturday to Wednesday', 'hours' => '8am to 5pm'],
                    ['name' => 'Dr. Khaled Makeen', 'department' => 'Dentistry', 'days' => 'Sunday to Friday', 'hours' => '11am to 5pm'],
                    ['name' => 'Dr. Shahira Ramadan', 'department' => 'Dermatology', 'days' => 'Monday to Friday', 'hours' => '8am to 2pm'],
                    ['name' => 'Dr. Eman Sorour', 'department' => 'Psychiatry', 'days' => 'Sunday to Friday', 'hours' => '11am to 5pm'],
                ],
            ],
            [
                'name' => 'Andalusia Maadi Hospital',
                'slug' => 'andalusia-maadi-hospital',
                'description' => 'Andalusia Maadi Hospital is a first-class private hospital located in the heart of Cairo. The hospital provides world-class medical care in a state-of-the-art facility. Services provided at Andalusia Maadi Hospital include general surgery, orthopedics, and many more.',
                'email' => 'contactus@andalusiagroup.net',
                'phone' => '10101',
                'address' => '4G/6, Sayed Anbar, Ezbet Fahmy, Maadi, Cairo Governorate, Egypt',
                'image' => 'andalusia.png',
                'doctors' => [
                    ['name' => 'Dr. Ahmed Fouad', 'department' => 'Cardiology', 'days' => 'Tuesday to Friday', 'hours' => '7am to 5pm'],
                    ['name' => 'Dr. Amal Sabry', 'department' => 'Surgery', 'days' => 'Sunday to Thursday', 'hours' => '5am to 12pm'],
                    ['name' => 'Dr. Amany Yahya Fadaily', 'department' => 'Neurology', 'days' => 'Thursday to Monday', 'hours' => '11am to 9pm'],
                    ['name' => 'Dr. Amira Ramadan', 'department' => 'Pediatric', 'days' => 'Monday to Saturday', 'hours' => '9am to 2pm'],
                    ['name' => 'Dr. Ehab Orfy', 'department' => 'Ear, Nose, and Throat', 'days' => 'Saturday to Tuesday', 'hours' => '6am to 3pm'],
                    ['name' => 'Dr. Ehab Ali', 'department' => 'Orthopedics', 'days' => 'Wednesday to Monday', 'hours' => '9am to 7pm'],
                    ['name' => 'Dr. Gamal Murad', 'department' => 'Ophthalmology', 'days' => 'Monday to Friday', 'hours' => '8am to 2pm'],
                    ['name' => 'Dr. Darine Mamdouh Azim', 'department' => 'Dentistry', 'days' => 'Sunday to Friday', 'hours' => '11am to 5pm'],
                    ['name' => 'Dr. Doaa Hebaa', 'department' => 'Dermatology', 'days' => 'Saturday to Tuesday', 'hours' => '8am to 2pm'],
                    ['name' => 'Dr. Tamer Eissa', 'department' => 'Psychiatry', 'days' => 'Sunday to Friday', 'hours' => '11am to 5pm'],
                ],
            ],
        ];

        foreach ($hospitals as $data) {
            Hospital::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }

    private function seedLabs(): void
    {
        $labs = [
            [
                'name' => 'Alfa Lab',
                'slug' => 'alfa-lab',
                'description' => 'Alfa Lab is one of the largest and most widespread medical analysis laboratories in the Arab Republic of Egypt. It applies the highest internationally recognized quality standards and has obtained the highest quality certificates in the field of medical analysis. It provides distinguished medical services to all its clients throughout the country.',
                'email' => 'care@alfalabs.com',
                'phone' => '16191',
                'address' => 'We have 220 branches in Egypt',
                'work_hours' => 'From 8:30 AM to 11 PM / Friday: from 9 AM to 5 PM',
                'image' => 'WhatsApp Image 2024-08-01 at 12.58.14 PM.jpeg',
                'xrays' => [
                    ['name' => 'X-RAY', 'cost' => 335],
                    ['name' => 'CT SCAN', 'cost' => 925],
                    ['name' => 'MRI', 'cost' => 450],
                    ['name' => 'ULTRASOUND-SONOGRAPHY', 'cost' => 600],
                    ['name' => 'PLAIN RADIOGRAPHY', 'cost' => 300],
                    ['name' => 'INTERVENTIONAL RADIOLOGY', 'cost' => 250],
                    ['name' => 'PANORAMIC X-RAY', 'cost' => 550],
                    ['name' => 'DEXA X-RAY', 'cost' => 400],
                ],
                'medical_tests' => [
                    ['name' => 'CBC', 'cost' => 335],
                    ['name' => 'BLOOD SUGAR TEST', 'cost' => 150],
                    ['name' => 'LIPID PROFILE', 'cost' => 400],
                    ['name' => 'LIVER FUNCTION TEST', 'cost' => 500],
                    ['name' => 'KIDNEY FUNCTION TEST', 'cost' => 350],
                    ['name' => 'THYROID FUNCTION TEST', 'cost' => 450],
                    ['name' => 'URINE ANALYSIS', 'cost' => 100],
                    ['name' => 'VITAMIN D TEST', 'cost' => 600],
                ],
            ],
            [
                'name' => 'Al Mokhtabar Lab',
                'slug' => 'al-mokhtabar-lab',
                'description' => 'Samples are collected in more than 214 branches covering the governorates of all regions in Egypt, and analyses are carried out in the main laboratory in addition to the central laboratories in the main governorates in Egypt.',
                'email' => 'info@almokhtabar.com',
                'phone' => '19014',
                'address' => '214+ branches across Egypt',
                'work_hours' => 'From 8 AM to 10 PM / Friday: from 9 AM to 5 PM',
                'image' => 'images (2).jpeg',
                'xrays' => [
                    ['name' => 'X-RAY', 'cost' => 300],
                    ['name' => 'CT SCAN', 'cost' => 900],
                    ['name' => 'MRI', 'cost' => 500],
                    ['name' => 'ULTRASOUND-SONOGRAPHY', 'cost' => 550],
                    ['name' => 'PLAIN RADIOGRAPHY', 'cost' => 280],
                    ['name' => 'INTERVENTIONAL RADIOLOGY', 'cost' => 300],
                    ['name' => 'PANORAMIC X-RAY', 'cost' => 500],
                    ['name' => 'DEXA X-RAY', 'cost' => 450],
                ],
                'medical_tests' => [
                    ['name' => 'CBC', 'cost' => 300],
                    ['name' => 'BLOOD SUGAR TEST', 'cost' => 120],
                    ['name' => 'LIPID PROFILE', 'cost' => 380],
                    ['name' => 'LIVER FUNCTION TEST', 'cost' => 480],
                    ['name' => 'KIDNEY FUNCTION TEST', 'cost' => 320],
                    ['name' => 'THYROID FUNCTION TEST', 'cost' => 420],
                    ['name' => 'URINE ANALYSIS', 'cost' => 90],
                    ['name' => 'VITAMIN D TEST', 'cost' => 550],
                ],
            ],
            [
                'name' => 'Cairo Clinical Lab',
                'slug' => 'cairo-clinical-lab',
                'description' => 'We take pride in our constant commitment to excellence, and always strive to achieve accurate and reliable results. Equipped with the latest technology and a team of experienced professionals to maintain the highest quality standards, we ensure that our services will meet and exceed your expectations.',
                'email' => 'info@cairoclinical.com',
                'phone' => '19445',
                'address' => 'Multiple branches in Cairo',
                'work_hours' => 'From 9 AM to 10 PM / Friday: from 10 AM to 4 PM',
                'image' => 'images (2).png',
                'xrays' => [
                    ['name' => 'X-RAY', 'cost' => 350],
                    ['name' => 'CT SCAN', 'cost' => 950],
                    ['name' => 'MRI', 'cost' => 480],
                    ['name' => 'ULTRASOUND-SONOGRAPHY', 'cost' => 620],
                    ['name' => 'PLAIN RADIOGRAPHY', 'cost' => 320],
                    ['name' => 'INTERVENTIONAL RADIOLOGY', 'cost' => 270],
                    ['name' => 'PANORAMIC X-RAY', 'cost' => 530],
                    ['name' => 'DEXA X-RAY', 'cost' => 420],
                ],
                'medical_tests' => [
                    ['name' => 'CBC', 'cost' => 320],
                    ['name' => 'BLOOD SUGAR TEST', 'cost' => 140],
                    ['name' => 'LIPID PROFILE', 'cost' => 420],
                    ['name' => 'LIVER FUNCTION TEST', 'cost' => 520],
                    ['name' => 'KIDNEY FUNCTION TEST', 'cost' => 360],
                    ['name' => 'THYROID FUNCTION TEST', 'cost' => 460],
                    ['name' => 'URINE ANALYSIS', 'cost' => 110],
                    ['name' => 'VITAMIN D TEST', 'cost' => 620],
                ],
            ],
        ];

        foreach ($labs as $data) {
            Lab::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }

    private function seedPharmacies(): void
    {
        $pharmacies = [
            [
                'name' => 'MISR Pharmacies',
                'slug' => 'misr-pharmacies',
                'description' => 'Welcome to Misr Pharmacies. Our Mission: To take care of our valued customers in an atmosphere of professionalism and respect by providing them with expert advice, high quality services and an unforgettable experience.',
                'email' => 'info@misr-online.com',
                'phone' => '19110',
                'address' => '58 branches across Egypt',
                'image' => 'images (3).png',
            ],
            [
                'name' => 'El Ezaby Pharmacies',
                'slug' => 'el-ezaby-pharmacies',
                'description' => "One of the nation's leading retail pharmacies; providing all you need of health and beauty products. Providing excellent health care services to the public, hospitals, and health insurance companies. Our mission is to take care of our valued customers in an atmosphere of professionalism and respect.",
                'email' => 'info@elezaby.com',
                'phone' => '19600',
                'address' => 'Multiple branches across Egypt',
                'image' => 'images (4).jpeg',
            ],
            [
                'name' => 'SEIF Pharmacies',
                'slug' => 'seif-pharmacies',
                'description' => "One of the nation's leading retail pharmacies; providing all you need of health and beauty products. Providing excellent health care services to the public, hospitals, and health insurance companies. Our mission is to take care of our valued customers in an atmosphere of professionalism and respect.",
                'email' => 'info@seifpharmacies.com',
                'phone' => '19199',
                'address' => 'Multiple branches across Egypt',
                'image' => 'images (1).png',
            ],
        ];

        foreach ($pharmacies as $data) {
            Pharmacy::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
