<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'General Medicine',
            'Cardiology',
            'Pediatrics',
            'Dermatology',
            'Neurology',
            'Orthopedics',
        ]);

        return [
            'name' => $name,
            'description' => fake()->paragraph(),
            'services' => fake()->sentences(4),
            'icon_path' => 'assets/images/Department/card.png',
            'hero_image_path' => 'assets/images/Department/cardiology-edit.jpg',
            'sidebar_image_path' => 'assets/images/Department/specialist-side-image.jpg',
            'contact_phone' => fake()->phoneNumber(),
            'contact_email' => fake()->safeEmail(),
            'is_active' => true,
        ];
    }
}
