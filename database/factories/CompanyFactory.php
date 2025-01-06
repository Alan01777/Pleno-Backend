<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'cnpj' => $this->faker->numerify('##############'),
            'legal_name' => $this->faker->company,
            'trade_name' => $this->faker->company,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'size' => $this->faker->randomElement(['MEI', 'EPP', 'EMP']),
            'user_id' => User::factory(),
        ];
    }
}
