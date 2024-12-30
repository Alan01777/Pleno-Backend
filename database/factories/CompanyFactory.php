<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'legal_name' => $this->faker->unique()->company,
            'trade_name' => $this->faker->company,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'size' => $this->faker->randomElement(['MEI', 'ME', 'EPP', 'EMP', 'EG']),
        ];
    }
}
