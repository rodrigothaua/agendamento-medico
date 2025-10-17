<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'patient_id' => \App\Models\Patient::inRandomOrder()->first()->id ?? \App\Models\Patient::factory(),
            'scheduled_at' => $this->faker->unique()->dateTimeBetween('-30 days', '+30 days'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }
}
