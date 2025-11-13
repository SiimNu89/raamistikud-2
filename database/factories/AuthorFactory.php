<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    protected $model = \App\Models\Author::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'birth_date' => $this->faker->date('Y-m-d', '2000-12-31'),
        ];
    }
}
