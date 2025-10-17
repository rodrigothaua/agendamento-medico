<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = $this->faker->randomElement(['pending', 'approved', 'failed']);
        
        // Pegar um agendamento que ainda não tem pagamento
        $appointmentId = \App\Models\Appointment::whereDoesntHave('payment')->inRandomOrder()->first()->id ?? \App\Models\Appointment::factory();
        
        return [
            'appointment_id' => $appointmentId,
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'method' => $this->faker->randomElement(['pix', 'credit_card']),
            'status' => $status,
            'paid_at' => $status === 'approved' ? $this->faker->dateTimeBetween('-30 days', 'now') : null,
            'transaction_id' => $status === 'approved' ? $this->faker->uuid() : null,
        ];
    }
}
