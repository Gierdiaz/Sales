<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition()
    {
        return [
            'name'    => $this->faker->name(),
            'phone'   => $this->faker->phoneNumber(),
            'email'   => $this->faker->unique()->safeEmail(),
            'number'  => $this->faker->numberBetween(1, 9999),
            'cep'     => $this->faker->postcode(),
            'address' => $this->faker->address(),
        ];
    }
}
